<?php

namespace Routing;

/**
 * A Route describes a route and its parameters.
 *
 */
class Route {
    /**
     * Nombre del Controlador y metodo a ejecutar
     * @var type 
     */
    private $controller;
    /**
     * Parametros obligatorios
     * @var type 
     */
    private $requirements;
    /**
     * Opciones de la ruta
     * @var type 
     */
    private $options;
    
    /**
     * Crea una ruta a partir del nombre del controlador y el metodo que llamara
     * @param type $controller Nombre del controlador y metodo a ejecutar Ejemplo:index
     * @param type $defaults Parametros obligatorios
     */
    function __construct($controller, $requirements = array (),$options = array()) {
        $this->setController($controller);
        $this->setRequirements($requirements);
        $this->setOptions($options);
    }

    private function setController($controller) {
        $this->controller = array();
        if(count($controller = explode(':', $controller)) != 2){
            throw new LogicException(sprintf('invalid format controller "%s", must be "Controller:method"', $controller));
        }
        $this->controller['controller'] = $controller[0];
        $this->controller['method'] = $controller[1];
        
    }

    public function getController() {
        return ucfirst($this->controller['controller']);
    }

    public function getMethod() {
        return $this->controller['method'];
    }

    public function getFullMethod() {
        return $this->controller['method'].'Action';
    }

    public function getRequirements() {
        return $this->requirements;
    }
    
    public function setOptions(array $options) {
        $validOptions = array('methods');
        foreach ($options as $option => $value) {
            if(!in_array($option, $validOptions)){
                throw new InvalidParameterException(sprintf('parameter option "%s" is not supported (%s)',$option,  implode(',', $validOptions)));
            }
        }
        $this->options = $options;
    }
    
    /**
     * Verifica que se cumplan las condiciones de la ruta
     * @return boolean
     */
    public function isValidOptions() {
        // Verifico si el metodo de acceso en el Request es el de la ruta
        if(isset($this->options['methods'])){
            $methods = $this->options['methods'];
            $method = $_SERVER['REQUEST_METHOD'];
            if(is_array($methods)){
                foreach ($methods as $value) {
                    if(!in_array($value, $methods)){
                        return false;
                    }
                }
            }else{
                if($method != $methods) {
                    return false;
                }
            }
        }
        return true;
    }

    private function setRequirements(array $requirements) {
        foreach ($requirements as $key => $value) {
            $requirements[$value] = $value;
            unset($requirements[$key]);
        }
        $this->requirements = $requirements;
    }
    
    const REGEX_DELIMITER = '#';
    
}

?>
