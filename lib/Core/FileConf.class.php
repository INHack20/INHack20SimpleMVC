<?php

namespace Core;

/**
 * Description of FileConf
 *
 * @author programacion4
 */
class FileConf {
    /**
     * Ruta donde se guardara el archivo
     */
    private $path = '../_conf/';
    /**
     * Nombre del archivo
     */
    private $fileName = 'conf.yml';
    /**
     * Contenido del archivo
     * @var array 
     */
    private $content = array();
    
    /**
     * Contenido del archivo que se guardara en el YAML
     * @param array $content
     */
    public function setContent(array $content) {
        $this->content = $content;
    }

    /**
     * Ruta completa del archivo con el nombre
     * @return string
     */
    private function getFile(){
        return $this->path.$this->fileName;
    }
    /**
     * 
     * @return type
     */
    private function getUrlRoot(){
        $mainUrlRoot = array();
        $http_origin = $_SERVER['HTTP_ORIGIN'];
        
        $php_self = explode('/',$_SERVER['PHP_SELF']);
        $phpPart = count($php_self);
        //Elimino la ruta de la carpeta de instalacion que son los dos ultimos niveles
        unset($php_self[--$phpPart]);
        unset($php_self[--$phpPart]);
        //Elimino la posicion 0 que normalmente esta vacia
        if(isset($php_self[0]) && $php_self[0] == ''){
            unset($php_self[0]);
        }
        $mainUrlRoot[]=$http_origin;
        $mainUrlRootMerge = array_merge($mainUrlRoot,$php_self);
        return $this->content['SIEMPRE_main_url_root'] = implode('/', $mainUrlRootMerge).'/';
    }
    
    private function getDocumentRoot() {
        $script_filename = explode('/', $_SERVER['SCRIPT_FILENAME']);
        $phpPart = count($script_filename);
        //Elimino la posicion 0 que normalmente esta vacia
        if(isset($script_filename[0]) && $script_filename[0] == ''){
            unset($script_filename[0]);
        }
        //Elimino la ruta de la carpeta de instalacion que son los dos ultimos niveles
        unset($script_filename[--$phpPart]);
        unset($script_filename[--$phpPart]);
        return $this->content['SIEMPRE_main_document_root'] = '/'.implode('/',$script_filename);
    }
    
    /**
     * Ruta donde se guardaran los documentos
     * @return string
     */
    private function getDataRoot() {
        $dirDataRoot = 'documents';
        $documentRoot = $this->getDocumentRoot();
        $documentRoot.= DS.$dirDataRoot;
        if(!defined('SIEMP_DATA_ROOT')){
            define('SIEMP_DATA_ROOT', $documentRoot);
        }
        return $this->content['SIEMPRE_main_data_root'] = $documentRoot;
    }
    
    private function getTypeDB() {
        return $this->content['SIEMPRE_main_db_type'] = 'pgsql';
    }
            
    private function getForceHttps() {
        return $this->content['SIEMPRE_main_force_https'] = false;
    }
    function write() {
        $this->getContent();
        @chmod($this->getFile(), 777);
        @unlink($this->getFile());
        $conf = Spyc::YAMLDump($this->content,4,80);
        $fo = fopen($this->getFile(), 'w');
        fwrite($fo, $conf);
        $write = fclose($fo);
        @chmod($this->getFile(), 0666);
        return $write;
    }
    
    public function getContent() {
        $this->getUrlRoot();
        $this->getDocumentRoot();
        $this->getDataRoot();
        $this->getTypeDB();
        $this->getForceHttps();
        return $this->content;
    }
}

?>
