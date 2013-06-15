<?php

namespace Loader;

/**
 * Description of Loader
 *
 * @author adcom23
 */
class Loader {
    
    private $prefixes = array();
            
    function addPrefix($prefix,$path){
        //var_dump($prefix.' '.$path);
        $this->prefixes[$prefix] = $path;
    }
    
    function loadBasePrefixes() {
        $pathLib = dirname(__DIR__);
        $dirHandle = opendir($pathLib);
        $exceptions = array('Loader','autoload.php','.','..');
        while($dir = readdir($dirHandle)){
            if(in_array($dir, $exceptions)){
                continue;
            }
            $path = $pathLib.DIRECTORY_SEPARATOR.$dir;
            $prefix = $dir;
            $this->addPrefix($prefix, $path);
        }
    }
    
    function register($prepend = false) {
        spl_autoload_register(array($this,'load'),true, $prepend);
    }
    
    function load($class) {
        if(($file = $this->findFile($class))){
            include $file;
            return true;
        }
    }
    
    private function findFile($class) {
        $libDir = dirname(__DIR__);
        $file = strtr($libDir.DIRECTORY_SEPARATOR.$class, '\\', DIRECTORY_SEPARATOR).'.php';
        if(file_exists($file)){
            return $file;
        }
        var_dump($file);
        \xdebug_print_function_stack();
    }
}

?>
