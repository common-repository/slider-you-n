<?php

/**
 * @package SliderYou-n
 * @version 1.2
 * @author giacomo@you-n.com
 */
/*
  Plugin Name: Slider You-n v 1.2
  Plugin URI:
  Description: A simple and full customizable Wordpress plugin for slideshow creation, responsive or none
  Author: giacomo@you-n.com
  Version: 1.2
  Author URI: http://www.you-n.com
 */

defined('PLUGIN_URL') or define('PLUGIN_URL', dirname(__FILE__));

defined('YS_PLUGIN_URI') or define('YS_PLUGIN_URI', plugins_url('', __FILE__));

defined('YSLIDER_SETTINGS') or define('YSLIDER_SETTINGS', 'Yslider_settings');

defined('YSLIDER_FOTO_SRCS') or define('YSLIDER_FOTO_SRCS', 'Yslider_foto_srcs');

if (!defined('YSLIDER_UPLOAD_PATH')) {
    $uploads_info = wp_upload_dir();
    $YsliderUpload = $uploads_info['basedir'];
    if (!is_dir($YsliderUpload . '/YouSlider')) {
        if (wp_mkdir_p($YsliderUpload . '/YouSlider')) {
            define('YSLIDER_UPLOAD_PATH', $YsliderUpload . '/YouSlider');
            wp_mkdir_p($YsliderUpload . '/YouSlider/thumbs/');
        } else {
            die('La cartella non è stata creata!!!!');
        }
    }
}
/**
 *  Classe del plugin
 */
class Yslider {

    function __construct() {

        // Create the settings array by merging the user's settings and the defaults
        $usersettings = (array) get_option(YSLIDER_SETTINGS);
        $defaultArray = $this->Ys_getBaseSetting();
        //check whether stored settings are compatible with current plugin version.
        //if not: overwrite stored settings
        $validSettings = $this->validateSettingsInDatabase($usersettings);
        if (!$validSettings) {
            $this->YsliderSettings = $defaultArray;
            update_option(YSLIDER_SETTINGS, $defaultArray);
        } else {
            $this->YsliderSettings = wp_parse_args($usersettings, $usersettings);
        }

        $userFoto = (array) get_option(YSLIDER_FOTO_SRCS);
        $dafaultArrayFoto = $this->checkFotoInDB($userFoto);
        if (!$dafaultArrayFoto) {
            
            $this->foto = $userFoto;
        } else {
            $campi=array(
                'id'=>0,
                'path'=>'',
                'title'=>'',
                'alt'=>'',
                'nome'=>''
            );
            $this->foto = wp_parse_args($userFoto, $campi); //
            update_option(YSLIDER_FOTO_SRCS, $campi);
        }

        if (is_admin()) {
            include_once 'core/YsliderBE.php';
            new Ys_BEPage($this->YsliderSettings, $this->foto);
        } else {
            //qui parte front!!
             include_once 'core/YsliderFront.php';
             new YS_FrontPage($this->YsliderSettings, $this->foto);
            
        }
        
    }
/**
 * Get default settings of plugin 
 * @return Array 
 */
    function Ys_getBaseSetting() {
        return array(
            'version' => '1.2',
            'active' => true,
            'width' => '625',
            'height' => '480',
            'speed' => '4000',
            'fx'=>'fade',
            'color'=>'#fff',
            'paginatore'=>true,
            'colorPaginatore'=>'#fff',
            'responsive'=>FALSE
        );
    }

    function validateSettingsInDatabase($settings) {
        if ($settings) {
            if (!array_key_exists('version', $settings)) {
                return false;
            }
        }
        return true;
    }
    /**
     * check if the option plugin is write in wp_options table
     * @param array $userFoto
     * @return boolean 
     */
    function checkFotoInDB($userFoto) {
        if ($userFoto) {
            if (!array_key_exists('id', $userFoto)) {
                return false;
            }
        }
        return true;
    }


}

new Yslider();
?>
