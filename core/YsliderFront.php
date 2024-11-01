<?php

/**
 * @author giacomo@you-n.com 
 * 07/13
 */
class YS_FrontPage {

    function __construct($settings, $foto) {
        $this->YsliderSettings = $settings;
        $this->foto = $foto;

        if ($this->YsliderSettings['active'] === TRUE) { //includo tutti gli script, inizializzo lo slider, lo visualkizzo, aggiungere i metodi!!!!!!!!!!!
            add_action('wp_enqueue_scripts', array(&$this, 'YS_SetScriptFront'));
            add_shortcode('YS-slider', array(&$this, 'YS_render_slider'));
        }
    }

    /**
     * Funzione per appendere gli script all'header del front
     * 
     * @param null
     * @return void 
     */
    function YS_SetScriptFront() {
        global $post;
        $isYS_page = get_post_meta($post->ID, 'YSslide', true);
        if ($isYS_page == 'on') {
            $width = '';
            if ($this->YsliderSettings['width'] == '100%') {
                $width = '100';
            }
            ?>
            <script type="text/javascript">
                //settare le proprietà de $quist, da passare a gestSliderFront.js
            <?php if ($this->YsliderSettings['width'] != '100%') { ?>
                    var widthResize= <?php echo $this->YsliderSettings['width']; ?>;
            <?php } ?>
                var heightResize= <?php echo $this->YsliderSettings['height']; ?>;
                var speed= '<?php echo $this->YsliderSettings['speed']; ?>';
                var fx ='<?php echo $this->YsliderSettings['fx']; ?>';
                var paginatore=false;
            <?php if ($this->YsliderSettings['paginatore'] == TRUE) { ?>
                    paginatore=true;
                    var colorePaginatore= '<?php echo $this->YsliderSettings['colorPaginatore'] ?>';
            <?php } ?>
                var Zindex = <?php echo sizeof($this->foto); ?>;
                var responsive=false;
            <?php if ($this->YsliderSettings['responsive'] == TRUE) { ?>
                    responsive=true;
            <?php } ?>
            </script>
            <?php
            wp_enqueue_script('jquery');
            wp_register_script('cycle', YS_PLUGIN_URI . '/js/cycle.js', array(), '1.0', TRUE);
            wp_register_script('cycleFront', YS_PLUGIN_URI . '/js/gestSliderFront.js', array(), '1.0', TRUE);
            wp_register_script('resize', YS_PLUGIN_URI . '/js/jResize.js', array(), '1.0', TRUE);
            wp_register_script('gestResize', YS_PLUGIN_URI . '/js/gestResize.js', array(), '1.0', TRUE);

            wp_register_style('YS_default', YS_PLUGIN_URI . '/css/YS_default-configuration.css', array(), 'screen');

            wp_enqueue_script('cycle');

            wp_enqueue_script('cycleFront');
            wp_enqueue_style('YS_default');
        }
    }
    /**
     * Render the slideshow
     * 
     * @param type $atts
     * @return type 
     */
    function YS_render_slider($atts) {
        extract(shortcode_atts(array(
                    'stato' => 'off'
                        ), $atts));
        return $this->getMySlider($atts['stato']);
    }

    /**
     * setta il contorno delle foto, e stampa nel front, nella posizione dello shortcode
     * @param type $stato
     * @return HTML 
     */
    function getMySlider($stato) {
        global $post;
        $str = '<div class="wrapper-general-YSlider">';
        $isYS_page = get_post_meta($post->ID, 'YSslide', true);
        $top = 0;
        $altezza = $this->YsliderSettings['height'];
        $width = $this->YsliderSettings['width'];
        $widthDiv = $width;
        if ($widthDiv != '100%') {
            $widthDiv.='px';
        }
        $classep = '';
        $classes = '';
        if ($this->YsliderSettings['color'] == '#ffffff') {
            $classes = 'black_n';
            $classep = 'black_p';
        }
        $top = ($altezza - 60) / 2;
        if ($stato == "on" && $isYS_page == 'on' && $this->YsliderSettings['active']) {
            ob_start();

            if ($this->YsliderSettings['responsive'] === TRUE) { //render responsive
                // $str.=' <div id="slider-content-misure">';
                $str.='<div class="clear" style="clear:both"></div><div id="slider-content"><div id="slider-elements">';
                $str.=$this->getFrontThumbnail($width, $altezza);
                $str.='</div>
                     <a class="arrows arrows-left ' . $classep . '"  style="top: ' . $top . 'px; background-color: ' . $this->YsliderSettings['color'] . '" id="prev"></a>
                    <a class="arrows arrows-right ' . $classes . '" style="top: ' . $top . 'px; background-color: ' . $this->YsliderSettings['color'] . '" id="next"></a>
                    </div><div class="clear" style="clear:both"></div>'; //chiude content ed elements
                // $str.='</div>'; //chiude misure
                $str.='</div>';
                if ($this->YsliderSettings['paginatore'] == TRUE) {
                    $str.='<div id="paginatore"></div><div class="clear" style="clear:both"></div>';
                }
            } else { //render coi cazzi
                $str.='<div id="slider-content-no-responsive">';
                $str.='<div id="YS-slider">';
                $str.=$this->getFrontThumbnail($width, $altezza);
                $str.='</div>'; //chiude wrapper immagini
                $str.=' <a class="arrows arrows-left ' . $classep . '"  style="top: ' . $top . 'px; background-color: ' . $this->YsliderSettings['color'] . '" id="prev"></a>
                    <a class="arrows arrows-right ' . $classes . '" style="top: ' . $top . 'px; background-color: ' . $this->YsliderSettings['color'] . '" id="next"></a>
                    <div class="clear" style="clear:both"></div>';
                $str.= '</div>';
                $str.='</div>';
                if ($this->YsliderSettings['paginatore'] == TRUE) {
                    $str.='<div id="paginatore"></div><div class="clear" style="clear:both"></div>';
                }
            }
            ob_end_clean();

            return $str;
        } else {

            return $str = '';
        }
    }

    /**
     * restituisce l'html contentente le miniature delle foto 
     * 
     * @param int $width width delle foto
     * @param int $heigth heigth delle foto
     * @return string 
     * 
     */
    function getFrontThumbnail($width, $heigth) {
        $str = '';
        $style = '';
        if ($this->YsliderSettings['responsive'] == TRUE) {
            $style = '';
        } else {
            $style.='style="width: ' . $width . 'px; height: ' . $heigth . 'px"';
        }

        foreach ($this->foto as $foto => $k) {

            foreach ($k as $i => $elemento) {
                $alt = $this->foto[$foto]['alt'];
                $title = $this->foto[$foto]['title'];
                if (in_array($i, array('path'))) {
                    $src = '';
                    if ($this->YsliderSettings['responsive'] == TRUE) {
                        $src = $elemento;
                    } else {
                        $src = $this->YS_get_cropped_thumb($elemento, $width, $heigth);
                    }
                    //$this->YS_get_cropped_thumb($elemento, $width, $heigth)
                    $str.= '<img class="resizzable" id="slider-foto_' . $foto . '" src="' . $src . '"  alt="' . $alt . '" title="' . $title . '" ' . $style . ' />';
                }
            }
        }
        return $str;
    }

    /**
     *
     * @param type $src il path assoluto dell'immagine
     * @param type $w la width
     * @param type $h l'altezza
     * @param type $zc parametro per la direzione del crop
     * @param type $q unused, la qualità
     * @return string  il path assoluto dell'immagine croppata
     */
    function YS_get_cropped_thumb($src = false, $w = 100, $h = 100, $zc = 0, $q = 75) {
        YS_PLUGIN_URI;
        if (!$src)
            return;

        $file = YS_PLUGIN_URI . '/script/tim/images/' . $w . '-' . $h . '-' . basename($src);
        $filepath = dirname(__FILE__) . '/script/tim/images/' . $w . '-' . $h . '-' . basename($src);
        if (file_exists($filepath))
            return $file;
        return YS_PLUGIN_URI . '/script/tim/timthumb.php?src=' . $src . '&w=' . $w . '&h=' . $h;
    }

}
?>
