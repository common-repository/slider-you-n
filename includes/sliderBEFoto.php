<?php
/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 * @author giacomo@you-n.com
 */
//var_dump($this->foto);
//$this->cancellaThumbs('thumbs');
//$this->cancellaTutto('/YouSlider/thumbs');
?>
<div class="YS-fase-2 goto-seleziona-foto">
    <h2>Seleziona Foto</h2> 
    <form method="post" action="admin-post.php" name="YsliderUpdateFoto" >
        <?php settings_fields(YSLIDER_FOTO_SRCS); ?>  

        <label class="form">Upload foto <span class="formPiccolo">(.jpg, .png)</span></label>
        <br />
        <div id="file-upload" style="float:left;margin-right: 10px;margin-bottom: 10px; background-color: #ccc">
            <div class="qq-uploader">
                <div class="qq-upload-drop-area" style="display: none; "> <span></span> </div>
                <div class="qq-upload-button" style="position: relative; overflow: hidden; direction: ltr;">
                    upload sostituire con BG
                    <input type="file" class="form" name="file" style="position: absolute; right: 0px; top: 0px; font-family: Arial; font-size: 12px; margin: 0px; padding: 0px; cursor: pointer; opacity:0;" />
                </div>
                <ul class="qq-upload-list">
                </ul>
            </div>
        </div>
        <div class="clear"></div>
        <input type="hidden" id="fileup" name="fileup" value="" />
        <div id="risposta"></div>
        <div class="clear"></div>
        <div id="result2"></div>
        <div class="clear"></div>
        <input type="hidden" name="<?php echo YSLIDER_FOTO_SRCS ?>[path]" id="srcFoto" value="" />
        <input type="hidden" name="<?php echo YSLIDER_FOTO_SRCS ?>[nome]" id="NomeFoto" value="" />
        <label for="TitleFoto">Titolo Foto:</label>
        <div class="clear"></div>
        <input  name="<?php echo YSLIDER_FOTO_SRCS ?>[title]" id="TitleFoto" />
        <div class="clear"></div>
        <label for="AltFoto">Alt foto:</label>
        <div class="clear"></div>
        <input  name="<?php echo YSLIDER_FOTO_SRCS ?>[alt]" id="AltFoto" />
        <div class="clear"></div>
        <input type="hidden" name="action" value="YsliderUpdateFoto"/>
        <input type="submit" name="YsliderUpdateFoto" id="YS-update-foto" class="YS-button" value="<?php _e('Inserisci elemento'); ?>"/>
        <input type="submit" name="YsliderDeleteFoto" id="YS-delete-foto" class="YS-button" value="<?php _e('Annulla caricamento'); ?>"/>
    </form>
    <?php
    if (isset($this->foto[0])) {
        if ($this->foto[0] != FALSE) {
            ?>
            <div id="informazioniFoto">
                <?php
                echo $this->getPhotosInfo();
                ?>
            </div>
            <div class="wrapper-slider" style="width: <?= $this->YsliderSettings['width'] ?>px; height: <?= $this->YsliderSettings['height'] ?>px">
                <div id="anteprimaFoto">
                    <?php
                    echo $this->getThumbnail($this->YsliderSettings['width'], $this->YsliderSettings['height'], false);
                    ?>
                </div>
                <?php
                $top = 0;
                $altezza = $this->YsliderSettings['height'];
                $top = ($altezza - 60) / 2;
                ?>
                <a style="top: <?= $top; ?>px;" href="#" id="prev">Precedente</a>
                <a style="top: <?= $top; ?>px;" href="#" id="next">Successiva</a>
            </div>
        </div>
        <div class="YS-fase-3 goto-ordine-foto">
            <h2>Scegli l'ordine di visualizzazione</h2>
            <div class="img-gallery-content left" id="paginatore-thumb" style="width: <?= $this->YsliderSettings['width'] ?>px;">
                <?php
                echo $this->getThumbnail(60, 60, true);
                ?>
            </div>
            <div class="clear"></div>
            <input type="submit" name="salvaordine" id="salvaOrdine" class="YS-button" value="Salva ordine foto"/>
            <div id="rispostaOrdine" ></div>
        <?php }else{
            ?>
            <div class="no-elements"><?php _e('Non sono ancora state inserite fotografie') ?></div>
            <?php
        }
    } else {
        ?>
        <div class="no-elements"><?php _e('Non sono ancora state inserite fotografie') ?></div>
<?php } ?>
</div>