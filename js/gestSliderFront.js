/** 
 * @author giacomo@you-n.com
 * 20/07/2013
 */
jQuery(document).ready(function($){ 
    if(Zindex>0){
        $('#prev, #next, #paginatore').css({
            "z-index":Zindex+3
        });
    }
    
    
    if(paginatore){//SI paginatore
      
        //inizio blocco per responsive
        $('#slider-elements').cycle({
            prev:'#prev',
            next: '#next',
            timeout: speed,
            fx:fx,
            containerResize: 1,
            pager:'#paginatore',
            width:'100%',
            height:'auto',
            fit:1,
            before: onBefore
        });
       
       
        
        $(window).resize(function(){
            if(responsive){
                $('#slider-elements').cycle('pause');
                $('#slider-elements').width('100%');
                $('#slider-elements').children("img").width('100%');
                $('#slider-elements').height('auto');
                $('#slider-elements').cycle('resume');
                var top = $('#slider-elements').children("img").height();
                var altezza=top;
                top= (top-62)/2
                $('#prev, #next, #paginatore').stop().animate({
                    "top":top
                },500);
                jQuery('.wrapper-general-YSlider').stop().animate({
                    "height":altezza
                },500)
            }
        })
        
        //FINE responsive
        
        $('#YS-slider').cycle({
            prev:'#prev',
            next: '#next',
            timeout: speed,
            fx:fx,
            width:widthResize,
            height:heightResize,
            pager:'#paginatore'
        })
        $('#paginatore a').css({
            "border":"1px solid "+colorePaginatore,
            "background-color":colorePaginatore
        });
    }else{ //NO paginatore
        if(Zindex>0){
            $('#prev, #next, #paginatore5').css({
                "z-index":Zindex+3
            });
        }
        //blocco responsive
        $('#slider-elements').cycle({
            prev:'#prev',
            next: '#next',
            timeout: speed,
            fx:fx,
            containerResize: 1,
            width:'100%',
            height:'auto',
            fit:1,
            before: onBefore
        });
        
        $(window).resize(function(){
            if(responsive){
                $('#slider-elements').cycle('pause');
                $('#slider-elements').width('100%');
                $('#slider-elements').children("img").width('100%');
                $('#slider-elements').height('auto');
                $('#slider-elements').cycle('resume');
                var altezza = $('#slider-elements').children("img").height();
                var top= (top-62)/2
                $('#prev, #next, #paginatore').stop().animate({
                    "top":top
                },500);
                jQuery('.wrapper-general-YSlider').stop().animate({
                    "height":altezza
                },500)
            }
        })
        //fine responsive
        
        //inizio blocco non responsive
        $('#YS-slider').cycle({
            prev:'#prev',
            next: '#next',
            timeout: speed,
            fx:fx,
            width:widthResize,
            height:heightResize
        })
    }
   
})
function onBefore(currSlideElement, nextSlideElement, options, forwardFlag){
    var altezza= jQuery(nextSlideElement).height();
    var top= (altezza-60)/2
    jQuery('#prev, #next, #paginatore').stop().animate({
        "top":top
    },500);
    jQuery('.wrapper-general-YSlider').stop().animate({
        "height":altezza
    },500)
}