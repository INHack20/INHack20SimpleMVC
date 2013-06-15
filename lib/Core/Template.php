<?php

namespace Core;

class Template {
    private $values;
       
    public function __construct() {
        $this->values = array();
    }
    
    public function assignValues($name, $value){
        $this->values[$name] = $value;
    }
    
    /**
    * Renderiza un formulario de un objeto
    * @param string $tpl Main:tpl
    */
    public function renderTplClass($template){
        global $bc, $conf, $db, $html, $user,$router;
        ob_start();
            extract($this->values);
            $template = explode(':', $template);
            $dir = ucfirst($template[0]);
            $file = strtolower($template[1]);
            include(BASE_PATH_TPL.$dir.DS.$file);
            $render = ob_get_contents();
        ob_end_clean();
        return $render;
    }
}
