<?php
/**
 * @author giacomo@you-n.com
 * 7/013
 * 
 */
//var_dump($this->YsliderSettings);
//$this->cancellaTutto('/YouSlider');
?>
<div id="icon-options-general" class="icon32"></div>
<h2>Slider You-n</h2> 
<br />
<br />
<div id="YS-descrizione-plugin">
    <h2>La pagina è composta da tre macro blocchi:</h2>
    <p>Il primo è dedicato alle configurazioni base dello slider: larghezza, altezza, velocità animazione, effetto di transizione, colore degli elementi utilizzati per scorrere le foto (frecce), 
        scelta tra paginatore abilitato o non abilitato con possibilità di configurarne il colore.</p>
    <p>Il secondo è formato dal modulo di upload delle foto che compongono lo slider, con possibilità 
        di impostare alt e title (obbligatori) per ogni immagine. E' possibile caricare una solo foto 
        per volta.</p>
    <p>Il terzo è composto da tutte le miniature che formano lo slider ed è stato implementato 
        per gestire l'ordine di visualizzazione, cambiare i tag alt e title ed eliminare l'immagine selezionata.</p>
    <br />
    <br />
    <h2>Per rendere visibile lo slider in una pagina si possono seguire due strade differenti:</h2>
    <p>Utilizzo dello shortcode: [YS-slider stato="on"] direttamente nell'editor di WordPress.</p>
    <p>Oppure, i developer di temi possono includerlo attraverso il PHP con:  echo do_shortcode('[YS-slider stato="on"]');</p>
    <br />
    <br />
    <div class="menu-interno-ys">
        <ul class="YS-indice-config">
            <li><a href="#" id="vai-configurazione">Configurazione</a></li>
            <li><a href="#" id="vai-seleziona-foto">Seleziona foto</a></li>
            <li><a href="#" id="vai-ordine-foto">Ordine foto</a></li>
        </ul>
    </div>
    <a class="you-logo" href="http://www.you-n.com" title="Visita il sito dell'autore" target="_blank"><img src="<?= YS_PLUGIN_URI ?>/images/you-n-logo.png" alt="You-n, agenzia di comunicazione integrata" title="Visita il sito web dell'autore" /></a>
    <div class="clear"></div>
    <a class="you-docs" href="<?php echo YS_PLUGIN_URI ?>/Manuale-Slider-You-n 1.0-Plugin-Wordpress.pdf" target="_blank">Leggi il manuale</a>
    <p>Please report all bugs to:<a href="mailto:info@you-n.com">info@you-n.com</a>, thanks</p>
</div>
<div class="clear"></div>
<br />
<br />
<div class="YS-fase-1 goto-configurazione">
    <h2>Configurazione</h2>

    <form method="post" id="YS-form-config" action="admin-post.php" name="YsliderUpdateSettings">
        <?php settings_fields(YSLIDER_SETTINGS); ?>  
        <?php //settings_fields(YSLIDER_FOTO_SRCS); ?>  
        <?php
        $checked = 'Non attivo';
        $classe = 'colore-classe-non-attivo';
        if ($this->YsliderSettings['active'] === true) {
            $checked = 'Attivo';
            $classe = 'colore-classe-attivo';
        }
        ?>
        <span>Stato Plugin nel tema: </span><span class="<?= $classe ?>"><?= $checked ?></span>
        <div class="clear"></div>
        <select id="attivo" name="<?php echo YSLIDER_SETTINGS ?>[active]">
            <option value="null">Seleziona</option>
            <option id="YS-opt-attivo" value="true">Attiva</option>
            <option id="YS-opt-disattivo" value="false">Disattiva</option>
        </select>
        <div class="helper-dialog"><p><?php _e('Qui puoi sceglie se attivare o disattivare il plugin da TUTTE le pagine in cui è stato incluso.<br />Se vuoi disattivarlo in una specifica pagina del tuo tema cancella lo shortcode, passa il parametro "off" allo shortcode oppure togli la spunta dalle opzioni');?></p></div>
        <div class="clear"></div>
        <label for="responsive">Grandezza dello slider:</label>
        <div class="clear"></div>
        <select id="YS-opt-responsive-select" name="<?php echo YSLIDER_SETTINGS ?>[responsive]">
            <option id="YS-opt-responsive" value="responsive">Responsive</option>
            <option id="YS-opt-custom" value="custom">Dimensioni custom</option>
        </select>
        <div class="helper-dialog"><p>Scegli le dimensioni del tuo slider: "Responsive" manipolerà le tue immagini in base alla dimensione della finestra; (scelta conigliata, specialmente per visualizzazione su cellulari e tablet) <br />Se imposti "Dimensioni custom" dovrai specificare larghezza e altezza delle immagini</p></div>
        <div class="clear"></div>
        <div id="wrapper-is-responsive">
            <div>
                <label for="width">Larghezza:</label>
                <div class="clear"></div>
                <input id="width" type="text" name="<?php echo YSLIDER_SETTINGS ?>[width]" value="<?= $this->YsliderSettings['width'] ?>" />
                <div class="helper-dialog"><p>Qui puoi impostare la larghezza delle foto.</p></div>
            </div>
            <div>
                <div class="clear"></div>
                <label for="height">Altezza:</label>
                <div class="clear"></div>
                <input id="height" type="text" name="<?php echo YSLIDER_SETTINGS ?>[height]" value="<?= $this->YsliderSettings['height'] ?>" />
                <div class="helper-dialog"><p>Qui puoi impostare l'altezza delle foto.</p></div>
            </div>
        </div>

        <div class="clear"></div>
        <?php
        $velocita = 'Non impostata';
        if ($this->YsliderSettings['speed'] > 0) {
            $velocita = $this->YsliderSettings['speed'];
        }
        ?>
       <!-- <span>
            Velocità animazione: <?= $velocita; ?>
        </span>-->
        <div class="clear"></div>
        <label for="velocita">Impostare velocità?</label>
        <br />
        <div class="clear"></div>
        <div class="clear"></div>
        <input type="text" id="durata-transizione" name="<?php echo YSLIDER_SETTINGS ?>[speed]" value="<?= $this->YsliderSettings['speed'] ?>" />
        <div class="helper-dialog"><p>(default: 400 millisecondi, lasciare a 0 per impostare il valore di default); Ricorda: 1 secondo = 1000 millisecondi</p></div>
        <div class="clear"></div>
        <label for="effetto">Effetto:</label>
        <div class="clear"></div>
        <select id="YS-fx" name="<?php echo YSLIDER_SETTINGS ?>[fx]">
            <option value="null">Seleziona</option>
            <option id="YS-opt-fade" value="fade">Fade</option>
            <option id="YS-opt-scrollUp" value="scrollUp">Scroll Up</option>
            <option id="YS-opt-scrollDown" value="scrollDown">Scroll Down</option>

            <option id="YS-opt-fadeZoom" value="fadeZoom">Fade Zoom</option>
            <option id="YS-opt-growX" value="growX">Grow X</option>
            <option id="YS-opt-growY" value="growY">Grow Y</option>
            <option id="YS-opt-scrollLeft" value="scrollLeft">Scroll Left</option>
            <option id="YS-opt-turnUp" value="turnUp">Turn Up</option>
            <option id="YS-opt-wipe" value="wipe">Wipe</option>
            <option id="YS-opt-zoom" value="zoom">Zoom</option>
            <option id="YS-opt-toss" value="toss">Toss</option>
        </select>
        <div class="helper-dialog"><p>Scegli l'effetto che prteferisci per il tuo slider. Effetto di default: feed</p></div>
        <div class="clear"></div>
        <label for="color-picker">Scegli un colore per le freccie dello slider:</label>
        <div class="clear"></div>
        <input id="color-picker" name="<?php echo YSLIDER_SETTINGS ?>[color]" value="<?= $this->YsliderSettings['color'] ?>" />
        <div class="helper-dialog"><p>Scegli il colore di sfondo delle freccie. Ricorda: il colore bianco ( #ffffff ) non è trasparente!</p></div>
        <div class="clear"></div>
        <label for="paginatore">
            Visualizzare il paginatore?
        </label>
        <div class="clear"></div>
        <select id="YS-opt-paginatore" name="<?php echo YSLIDER_SETTINGS ?>[paginatore]">
            <option value="null">Seleziona</option>
            <option id="YS-opt-p-attivo" value="attivo">Si</option>
            <option id="YS-opt-p-disattivo" value="disattivo">No</option>
        </select>
        <div class="helper-dialog"><p>Se preferisci visualizzare il paginatore sotto lo slider, seleziona Si</p></div>
        <div class="clear"></div>
        <div id="wrapper-color-paginatore">
            <label for="color-picker-paginatore">Scegli il colore del paginatore:</label>
            <div class="clear"></div>
            <input id="color-picker-paginatore" name="<?php echo YSLIDER_SETTINGS ?>[colorPaginatore]" value="<?= $this->YsliderSettings['colorPaginatore'] ?>" />
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <input type="hidden" name="action" value="YsliderUpdateSettings"/>

        <input type="submit" id="YS-submitConfig" name="YsliderUpdateSettings" class="YS-button" value="<?php _e('Save Changes'); ?>"/>
    </form>
</div>