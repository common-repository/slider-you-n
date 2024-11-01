<?php

/**
 * @author giacomo@you-n.com
 * 07/13 
 */
class Ys_BEPage {

    function __construct($settings, $foto) {
        $this->YsliderSettings = $settings;
        $this->foto = $foto;
        add_action('admin_menu', array($this, 'addYmenu'), 11);
        add_action('admin_post_YsliderUpdateSettings', array(&$this, 'YsliderUpdateSettings'));
        add_action('admin_post_YsliderUpdateFoto', array(&$this, 'YsliderUpdateFoto'));

        add_action("wp_ajax_uploadYfoto", array(&$this, "uploadYfoto"));
        add_action("wp_ajax_YS-saveOrder", array(&$this, "YS_SaveNewOrder"));
        add_action("wp_ajax_getNewSliderOrder", array(&$this, "getNewSliderOrder"));
        add_action("wp_ajax_updateAttributes", array(&$this, "updateAttributes"));
        add_action("wp_ajax_deleteFoto", array(&$this, "deleteFoto"));
        add_action("wp_ajax_getNewSliderConfiguration", array(&$this, "getNewSliderConfiguration"));
        add_action("wp_ajax_YS-deleteUploadedFoto", array(&$this, "YSdeleteUploadedFoto"));

        add_action('admin_menu', array(&$this, 'YSAddBoxAdmin'));
        add_action('save_post', array(&$this, 'YS_SaveDataAdm'));

        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'yslider')
            add_action('admin_enqueue_scripts', array(&$this, 'YsliderScriptsEnqueue'));
    }

    function YSAddBoxAdmin() {
        global $meta_box;
        $meta_box = array(
            'id' => 'slide',
            'title' => 'Slider You-n',
            'page' => 'page',
            'context' => 'side',
            'priority' => 'low',
            'fields' => array(
                array(
                    'name' => 'Mostra slideshow nella pagina',
                    'id' => 'YSslide',
                    'type' => 'checkbox'
                )
            )
        );
        add_meta_box($meta_box['id'], $meta_box['title'], array(&$this, 'YS_GestBoxAdmin'), $meta_box['page'], $meta_box['context'], $meta_box['priority']);
    }

    function YS_GestBoxAdmin() {
        global $post, $meta_box;
        echo '<input type="hidden" name="sight_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

        echo '<table class="form-table">';

        foreach ($meta_box['fields'] as $field) {
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);
            $checked = '';
            if ($meta != '')
                $checked = 'checked="checked"';
            echo '<tr>',
            '<th style="width:50%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
            '<td>';
            echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '" ' . $checked . ' />';
            echo '<td>',
            '</tr>';
        }

        echo '</table>';
    }

    function YS_SaveDataAdm() {
        global $meta_box, $post;
        if (isset($post) && $post->ID) {
            if (!wp_verify_nonce($_POST['sight_meta_box_nonce'], basename(__FILE__))) {
                return $post->ID;
            }
            // check autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post->ID;
            }

            // check permissions
            if ('page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post->ID;
                }
            } elseif (!current_user_can('edit_post', $post_id)) {
                return $post->ID;
            }
            foreach ($meta_box['fields'] as $field) {
                $old = get_post_meta($post->ID, $field['id'], true);
                $new = $_POST[$field['id']];

                if ($new && $new != $old) {
                    update_post_meta($post->ID, $field['id'], $new);
                } elseif ('' == $new && $old) {
                    delete_post_meta($post->ID, $field['id'], $old);
                }
            }
            return;
        }
        return;
    }

    function YsliderScriptsEnqueue() {
        $url = admin_url('admin-ajax.php');
        $attivoDisattivo = $this->YsliderSettings['active'];
        if ($attivoDisattivo) {
            $attivoDisattivo = 'attivo';
        } else {
            $attivoDisattivo = 'disattivo';
        }
        $urloModal = YS_PLUGIN_URI;
        $paginatore = $this->YsliderSettings['paginatore'];
        if ($paginatore) {
            $paginatore = 'attivo';
        } else {
            $paginatore = 'disattivo';
        }
        ?>
        <script type="text/javascript">
            var urlo = '<?php echo $url; ?>';
            var Zindex = <?php echo sizeof($this->foto); ?>;
            var attivoDisattivo = '<?php echo $attivoDisattivo; ?>' ;
            var optSlider='<?php echo $this->YsliderSettings['fx']; ?>';
            var urloModal = '<?php echo $urloModal; ?>';
            var optPaginatore= '<?php echo $paginatore; ?>';
            var speed= '<?php echo $this->YsliderSettings['speed']; ?>';
            var fx ='<?php echo $this->YsliderSettings['fx']; ?>';
            var responsive;
        <?php if ($this->YsliderSettings['responsive'] == TRUE) { ?>
                responsive='responsive';
        <?php } else { ?>
                responsive='custom'
        <?php } ?>
        </script>
        <?php
        wp_register_script('uploader', YS_PLUGIN_URI . '/js/fileuploader.js', array(), '1.0', TRUE);
        wp_register_script('GestUploader', YS_PLUGIN_URI . '/js/gestUpload.js', array(), '1.0', TRUE);
        wp_register_script('cycle', YS_PLUGIN_URI . '/js/cycle.js', array(), '1.0', TRUE);
        wp_register_script('GestSliderBE', YS_PLUGIN_URI . '/js/gestSlider.js', array(), '1.0', TRUE);
        wp_register_script('blockUI', YS_PLUGIN_URI . '/js/blockUI.js', array(), '1.0', TRUE);
        wp_enqueue_script('uploader');
        wp_enqueue_script('GestUploader');
        wp_enqueue_script('cycle');
        wp_enqueue_script('GestSliderBE');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('blockUI');
        wp_enqueue_script('iris');


        add_action('admin_head', 'custom_scripts');

        function custom_scripts() {
            echo '<link rel="stylesheet" type="text/css" href="' . YS_PLUGIN_URI . '/css/default.css" />';
        }

    }

    /**
     * aggiunge la voce di menù
     * @param null
     * 
     * @return null
     *  
     */
    function addYmenu() {
        if (function_exists('add_menu_page')) {
            $messaggio = '';
            add_menu_page('Slider You-n', 'Slider You-n', 'administrator', 'yslider', array(&$this, 'renderAdminPage'), YS_PLUGIN_URI . '/images/you-slider-menu-thumb.png', 26); //PASSAGGIO PER RIFERIMENTO
            if (isset($_GET['YsliderUpdateSettings']) || isset($_GET['YsliderUpdateFoto'])) {
                $messaggio = 'Modifiche Apportate con successo!';
            }

            $this->showMyMessage($messaggio);
        }
    }

    /**
     * chiama il php che genera la pagina Home del plugin
     * @subpackage home.php
     * 
     * 
     * @param null
     * 
     * @return null 
     */
    function renderAdminPage() {
        include PLUGIN_URL . '/includes/sliderBEConf.php';
        include PLUGIN_URL . '/includes/sliderBEFoto.php';
    }

    /**
     * Prende i dai passati in POST, e li aggiorna sul DB
     * @param void
     * @return void, viene eseguito un redirect
     * @uses updateSettingsInDatabase()
     */
    function YsliderUpdateSettings() {
        if (!current_user_can('manage_options'))
            wp_die('Non hai i permessi per modificare le opzioni del plugin!');

        $attivo = $_POST[YSLIDER_SETTINGS]['active'];
        $fx = $_POST[YSLIDER_SETTINGS]['fx'];
        if ($attivo == 'null')
            $attivo = TRUE;

        if ($attivo == 'false')
            $attivo = FALSE;

        if ($attivo == 'true')
            $attivo = TRUE;
        if ($fx == 'null') {
            $fx = 'fade';
        }
        $paginatore = $_POST[YSLIDER_SETTINGS]['paginatore'];
        $pagOpt;
        if ($paginatore == 'null') {
            $pagOpt = false;
        }
        if ($paginatore == 'attivo') {
            $pagOpt = true;
        }
        if ($paginatore == "disattivo") {
            $pagOpt = false;
        }
        $colorePaginatore = $_POST[YSLIDER_SETTINGS]['colorPaginatore'];

        $responsive = $_POST[YSLIDER_SETTINGS]['responsive'];
        $isResp;
        if ($responsive == "responsive") {
            $isResp = TRUE;
        }
        if ($responsive == "custom") {
            $isResp = FALSE;
        }

        $width = $_POST[YSLIDER_SETTINGS]['width'];
        $campi = array(
            'version' => '0.2', //la metto a mano perchè non la passo in post
            'active' => $attivo,
            'width' => $width,
            'height' => $_POST[YSLIDER_SETTINGS]['height'],
            'speed' => $_POST[YSLIDER_SETTINGS]['speed'],
            'fx' => $fx,
            'color' => $_POST[YSLIDER_SETTINGS]['color'],
            'paginatore' => $pagOpt,
            'colorPaginatore' => $colorePaginatore,
            'responsive' => $isResp
        );


        $this->YsliderSettings = $campi;
        $this->updateSettingsInDatabase();
        $referrer = str_replace(array('&YsliderUpdateSettings', '&YsliderDeleteSettings'), '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&YsliderUpdateSettings');
    }

    /**
     * Aggiorna la configurazione dell'oggetto $this->YsliderSettings sul DB 
     * @param void
     * @return void
     */
    function updateSettingsInDatabase() {
        update_option(YSLIDER_SETTINGS, $this->YsliderSettings);
    }

    /**
     * Aggiunbge all'array delle opzioni la nuova foto inserita
     * @param VOID
     * @return void
     */
    function YsliderUpdateFoto() {
        if (!current_user_can('manage_options'))
            wp_die('Non hai i permessi per modificare le opzioni del plugin!');
        $idFoto;
        $max = 0;
        $flag = false;
        if ($this->foto[0] === FALSE) { //primo inserimento
            $idFoto = 0;
        } else {
            //CALCOLARE IL PRIMO ID LIBERO!!
            foreach ($this->foto as $k => $el) {//devo verificarlo: posso aver canellato una foto "immezzo"
                if ($k >= $max)
                    $max = $k;
            }
            $idFoto = ++$max;
            $flag = TRUE;
        }
        $campi = array(
            //  'id' => $idFoto, //non lo passo dalla form, incremento a mano
            'path' => $_POST[YSLIDER_FOTO_SRCS]['path'],
            'title' => $_POST[YSLIDER_FOTO_SRCS]['title'],
            'alt' => $_POST[YSLIDER_FOTO_SRCS]['alt'],
            'nome' => $_POST[YSLIDER_FOTO_SRCS]['nome']
        );
        /*
         * Conservo i vecchi valori e appendo i nuovi 
         * 
         */
        if ($flag) { //inserimenti successivi
            $flag = FALSE;
            $this->foto[$idFoto] = $campi;
        } else { //primo inserimento
            $this->foto[$idFoto] = $campi;
        }

        $this->updateFotoObj();
        $referrer = str_replace(array('&YsliderUpdateFoto', '&YsliderUpdateFoto'), '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&YsliderUpdateFoto');
    }

    /**
     * Aggiorna l'ordine dello slider
     * @param void
     * @return JSON con messaggio più stato
     */
    function YS_SaveNewOrder() {
        $res = Array('OK' => true, 'message' => '');
        if (!current_user_can('manage_options'))
            wp_die('Non hai i permessi per modificare le opzioni del plugin!');

        $newArray = $_POST['newOrder'];
        if (strlen($newArray) == 0) {
            $res['OK'] = false;
            $res['message'] = "<span class='errore-save-order'>Si è verificato un errore, riprovare.</span>";
        } else {
            $ids = explode(',', $newArray);

            $nuovo = array();
            for ($i = 0; $i < sizeof($ids); $i++) {
                $nuovo[] = $this->foto[$ids[$i]];
            }
            $this->foto = $nuovo;

            if ($this->updateFotoObj()) {
                $res['message'] = "<span class='save-order-ok'>Cambio ordine effettuato!</span>";
            } else {
                $res['OK'] = false;
                $res['message'] = "<span class='errore-save-order'>Si è verificato un errore durante il salvataggio, ti preghiamo di riprovare</span>";
            }
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Aggiorna, tramite JSON la galleria visualizzata nella pagina di amministrazione
     * 
     * @param void
     * @return JSON html più stato 
     */
    function getNewSliderOrder() {
        $res = Array('OK' => true, 'message' => '');
        $mess = '';
        foreach ($this->foto as $foto => $k) {
            foreach ($k as $i => $elemento) {
                if (in_array($i, array('path')))
                    $mess.= '<img id="slider-foto_' . $foto . '" src="' . $elemento . '" width="' . $this->YsliderSettings['width'] . '" height="' . $this->YsliderSettings['height'] . '"  />';
            }
        }
        if (strlen($mess) > 1) {
            $res['message'] = $mess;
        } else {
            $res['OK'] = false;
            $res['message'] = "Si è verificato un errore, ti preghiamo di riprovare";
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Aggiorna, tramite JSON la galleria visualizzata nella pagina di amministrazione e le thumb per gestire la galleria.
     * Devo aggiornare entrambi per non far visualizzare la miniatura
     * @param void
     * @return JSON html più stato 
     */
    function getNewSliderConfiguration() {
        $res = Array('OK' => true, 'message' => '', 'thumb' => '', 'info' => '');
        $mess = '';
        $thumb = '';
        if (isset($this->foto[0]) && $this->foto[0] != '') {
            $mess.= $this->getThumbnail($this->YsliderSettings['width'], $this->YsliderSettings['height'], false);
            $thumb.= $this->getThumbnail(60, 60, true);

            $info = $this->getPhotosInfo();
            if (strlen($mess) > 1 && strlen($thumb) > 1) {
                $res['message'] = $mess;
                $res['thumb'] = $thumb;
                $res['info'] = $info;
            } else {
                $res['OK'] = false;
                $res['message'] = "Si è verificato un errore, ti preghiamo di riprovare";
            }
        } else {
            delete_option(YSLIDER_FOTO_SRCS);
            $res['empty'] = true;
            $res['message'] = "La galleria è vuota";
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Aggiorna alt e title di una foto
     * 
     * @param: null
     * 
     * @return: JSON; html+messaggio oppure messaggio e stato di errore 
     */
    function updateAttributes() {
        $res = Array('OK' => true, 'message' => '', 'data' => '');

        $id = $_POST['id'];
        $alt = $_POST['alt'];
        $title = $_POST['title'];
        if ($this->foto[$id]['title'] == $title && $this->foto[$id]['alt'] == $alt) { //simulo l'inserimento: update_option restituisce FALSE anche in caso di non aggiornamento per dati uguali
            $res['message'] = "Modifica effettuata con successo.";
            $res['OK'] = false;
            $info = $this->getPhotosInfo();
            $res['data'] = $info;
        } else {
            $this->foto[$id]['title'] = $title;
            $this->foto[$id]['alt'] = $alt;
            if ($this->updateFotoObj()) {
                $info = $this->getPhotosInfo();
                $res['message'] = "Modifica effettuata con successo.";
                $res['data'] = $info;
            } else {
                $res['message'] = "Si sono verificati problemi durante l'aggiornamento, riprova.";
                $res['OK'] = false;
            }
        }

        echo json_encode($res);
        exit;
    }

    /**
     * restituisce le informazioni delle foto
     * 
     * @param null
     * 
     * @return html 
     */
    function getPhotosInfo() {
        $str = '';
        foreach ($this->foto as $foto => $k) {
            $str.= '<div id="info-foto_' . $foto . '">';
            foreach ($k as $i => $elemento) {
                if (in_array($i, array('title')))
                    $str.= '<div>Titolo foto:<div class="clear"></div> <input id="modal-title-foto" type="text" class="titleFoto" value="' . $elemento . '"  name="titleFoto" /><div class="clear"></div></div>';
                if (in_array($i, array('alt')))
                    $str.= '<div>Alt foto:<div class="clear"></div> <input id="modal-alt-foto" type="text" class="altFoto" value="' . $elemento . '" name="altFoto" /><div class="clear"></div></div>';
                if (in_array($i, array('path')))
                    continue;
            }
            $str.= "</div>";
        }
        return $str;
    }

    /**
     * restituisce l'html contentente le miniature delle foto in caso $isThumb è true, immagini per lo slider di test in caso negativo
     * 
     * @param int $width width delle foto
     * @param int $heigth heigth delle foto
     * @param bool $isThumb
     * @return string 
     */
    function getThumbnail($width, $height, $isThumb) {
        $str = '';
        foreach ($this->foto as $foto => $k) {

            foreach ($k as $i => $elemento) {
                if (in_array($i, array('path'))) {
                    if ($isThumb)
                        $str.='<div class="wrapper-thumb"><div class="area-modifica"></div><div class="area-cancellami"></div><div class="area-wrap"></div>';

                    $str.= '<img id="slider-foto_' . $foto . '" src="' . $this->YS_get_cropped_thumb($elemento, $width, $height) . '" width="' . $width . '" height="' . $height . '"  />';

                    if ($isThumb)
                        $str.='</div>';
                }
            }
        }
        return $str;
    }

    function YSdeleteUploadedFoto() {
        $res = Array('OK' => true, 'message' => '');
        if (!current_user_can('manage_options'))
            wp_die('Non hai i permessi per modificare le opzioni del plugin!');

        $nomeImg = $_POST['nomeImg'];
        if (strlen($nomeImg) > 0) {
            /*
             * RIDEFINISCO IL PATH DI UPLOAD 
             * con la chiamata ajax non ho la classe istanziata!!!!
             */
            $uploads_info = wp_upload_dir();
            $YsliderUpload = $uploads_info['basedir'];
            $YsliderUpload.='/YouSlider/';
            $YsliderUploadThumb = $YsliderUpload . 'thumbs/';
            if (file_exists($YsliderUploadThumb . $nomeImg) && file_exists($YsliderUpload . $nomeImg)) {
                // echo sizeof($this->foto);exit;
                if (unlink($YsliderUploadThumb . $nomeImg) && unlink($YsliderUpload . $nomeImg)) {
                    $res['OK'] = TRUE;
                    $res['message'] = "Elemento cancellato con successo";
                } else {
                    $res['OK'] = FALSE;
                    $res['message'] = "Si è verificato un errore in fase di cancellazione";
                }
            } else {
                $res['OK'] = FALSE;
                $res['message'] = "Si è verificato un errore: il file non esiste!";
            }
        } else {
            $res['OK'] = false;
            $res['message'] = 'Forse non dovresti essere qui';
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Funzione per cancellare la foto selezionata
     * 
     * @param null
     * @return string JSON 
     */
    function deleteFoto() {
        $res = Array('OK' => true, 'message' => '', 'data' => '');
        if (!current_user_can('manage_options'))
            wp_die('Non hai i permessi per modificare le opzioni del plugin!');

        $id = $_POST['idFoto'];

        if ($id >= 0) {
            $nomeImg = $this->foto[$id]['nome'];
            if (strlen($nomeImg) > 0) {
                /*
                 * RIDEFINISCO IL PATH DI UPLOAD 
                 * con la chiamata ajax non ho la classe istanziata!!!!
                 */
                $uploads_info = wp_upload_dir();
                $YsliderUpload = $uploads_info['basedir'];
                $YsliderUpload.='/YouSlider/';
                $YsliderUploadThumb = $YsliderUpload . 'thumbs/';
                if (file_exists($YsliderUploadThumb . $nomeImg) && file_exists($YsliderUpload . $nomeImg)) {
                    if (unlink($YsliderUploadThumb . $nomeImg) && unlink($YsliderUpload . $nomeImg)) {
                        if (sizeof($this->foto) > 1) {
                            unset($this->foto[$id]);
                            $nuovo = array();
                            foreach ($this->foto as $foto => $el) { //ricreo gli indici ordinati per non lasciare "buchi"
                                $nuovo[] = $this->foto[$foto];
                            }
                            $this->foto = $nuovo;
                        } else {//era l'ultima foto!!
                            unset($this->foto);
                            $this->foto = false;
                        }
                        if ($this->updateFotoObj()) {
                            //aggiornare in front!!
                            $res['message'] = "L'immagine è stata eliminata";
                        } else {
                            $res['OK'] = FALSE;
                            $res['message'] = "Si è verificato un errore in fase di riordinamento";
                        }
                    } else {
                        $res['OK'] = FALSE;
                        $res['message'] = "Si è verificato un errore in fase di cancellazione";
                    }
                } else {
                    $res['OK'] = FALSE;
                    $res['message'] = "Si è verificato un errore: il file non esiste!";
                }
            } else {
                $res['OK'] = FALSE;
                $res['message'] = "Si è verificato un errore o l'immagine è inesistente";
            }
        } else {
            $res['OK'] = false;
            $res['message'] = "Forse non dovresti essere qui";
        }
        echo json_encode($res);
        exit;
    }

    /**
     * Svuta ricorsivamente le cartelle con le foto
     * @param string $dir directory da svuotare e. YouSlider, thumbs
     * 
     * @return void 
     */
    function cancellaTutto($dir) {
        $uploads_info = wp_upload_dir();
        $YsliderUpload = $uploads_info['basedir'];
        $YsliderUpload.=$dir;
        //$YsliderUploadThumb = $YsliderUpload . 'thumbs/';

        if ($objs = @glob($YsliderUpload . "/*")) {
            foreach ($objs as $obj) {
                @is_dir($obj) ? $this->cancellaTutto($obj) : @unlink($obj);
            }
        }
    }

    /**
     * Funzione per visualizzare il messaggio di conferma attuazione modifiche
     * 
     * @param string $messaggio
     * @return HTML
     * @uses funzione anonima 
     */
    function showMyMessage($messaggio) {
        if ($messaggio != '') {
            $msg = '<div class="updated fade"><p>' . $messaggio . '</p></div>';
            add_action('admin_notices', create_function('', "echo '$msg';"));
        }
    }

    /**
     * Funzione per aggiornare l'oggetto $this->foto nelle opzioni di wordpress
     * 
     * @param void
     * 
     * @return boolean 
     * 
     */
    function updateFotoObj() {
        if (update_option(YSLIDER_FOTO_SRCS, $this->foto)) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * Upload foto in AJAX
     * @param void
     * @return JSON parsificato in HTML
     */
    function uploadYfoto() {

        /*
         * RIDEFINISCO IL PATH DI UPLOAD 
         * con la chiamata ajax non ho la classe istanziata!!!!
         */
        $uploads_info = wp_upload_dir();
        $YsliderUpload = $uploads_info['basedir'];
        $YsliderUpload.='/YouSlider/';
        $pathAssoluto = $uploads_info['baseurl'] . '/YouSlider/';


        $allowedExtensions = array("jpg", "png");
// max file size in bytes
        $sizeLimit = 2.5 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $path = $YsliderUpload; //attenzione in produzione!!!!!!!
        $result = $uploader->handleUpload($path);
// to pass data through iframe you will need to encode all html tags
        if (isset($result['success'])) {

            $img = $pathAssoluto . $result['file'];
            $result['newImg'] = crea_thumbnail($img, $path, $pathAssoluto, 600);
        }
        $result['path'] = $pathAssoluto . $result['file'];
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    function uploadFotoInDB() {
        update_option(YSLIDER_FOTO_SRCS, $this->foto);
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

/**
 * PARTE DI FILE UPLOLAD!!! 
 */

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()) {
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    function getName() {
        return $_GET['qqfile'];
    }

    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
            return false;
        }
        return true;
    }

    function getName() {
        return $_FILES['qqfile']['name'];
    }

    function getSize() {
        return $_FILES['qqfile']['size'];
    }

}

class qqFileUploader {

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760) {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings() {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str) {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE) {
        if (!is_writable($uploadDirectory)) {
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file) {
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = str_replace(" ", "-", $pathinfo['filename']);
        $filename .='-';
        $filename .= date('Ymdh'); //md5(uniqid());
        $ext = $pathinfo['extension'];

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
        }

        if (!$replaceOldFile) {
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 9999);
            }
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
            return array('success' => true, 'file' => $filename . '.' . $ext);
        } else {
            return array('error' => 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
    }

}

function crea_thumbnail($image, $pathImmagine, $pathAssoluto, $dim = 150) {

    $size = getimagesize($image);
    $dim = 150;
    switch ($size[2]) {
        case 1:
            $tmb = ImageCreateFromGif($image);
            break;
        case 2:
            $tmb = ImageCreateFromJpeg($image);
            break;
        case 3:
            $tmb = ImageCreateFromPng($image);
            break;
        default:
            $size[0] = $size[1] = $dim;
            $tmb = ImageCreateTrueColor($dim, $dim);
            $rosso = ImageColorAllocate($tmb, 255, 0, 0);
            ImageString($tmb, 5, 10, 10, "Questa non e`", $rosso);
            ImageString($tmb, 5, 10, 30, "un'immagine", $rosso);
            ImageString($tmb, 5, 10, 50, "GIF, JPEG o PNG", $rosso);
            ImageString($tmb, 5, 10, 70, "valida.", $rosso);
    }

    if ($size[0] < $dim and $size[1] < $dim) {
        $new_w = $size[0];
        $new_h = $size[1];
    } else if ($size[0] > $size[1]) {
        $new_w = $dim;
        $new_h = $dim * $size[1] / $size[0];
    } else {
        $new_w = $dim * $size[0] / $size[1];
        $new_h = $dim;
    }

    $print_tmb = ImageCreateTrueColor($new_w, $new_h);
    imagecopyresampled($print_tmb, $tmb, 0, 0, 0, 0, $new_w, $new_h, ImageSX($tmb), ImageSY($tmb));

    // header("Content-type: image/jpeg");
    $newimg = $pathImmagine . "thumbs/" . basename($image);
    ImageJpeg($print_tmb, $newimg, 100);
    Imagedestroy($print_tmb);
    return $pathAssoluto . "thumbs/" . basename($image);
}

/*
 * FINE PARTE FILE UPLOAD!!
 */
?>
