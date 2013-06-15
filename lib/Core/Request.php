<?php
namespace Core;

/**
 * Description of request
 *
 * @author programacion4
 */
class Request {
    
    /**
     * Devuelve el valor de un parametro pasado por GET o POST
     * @param string $parameter
     * @return $parameter
     */
    public static function getParameter($parameter) {
        if(isset($_REQUEST[$parameter])) {
            return $_REQUEST[$parameter];
        }
        else
            return '';
    }
    
    /**
     *  Retorna el parametro que se paso pero solo por POST
     *  @param type $parameter
     *  @return string
     */
    public static function getParameterPost($parameter){
        if(isset($_POST[$parameter])) {
            return $_POST[$parameter];
        }
        else
            return '';
    }
    
    /**
     *  Retorna el parametro pero que solo se paso por GET
     * @param type $parameter
     * @return string
     */
    public static function getParameterGet($parameter){
        if(isset($_GET[$parameter])) {
            return $_GET[$parameter];
        }
        else
            return '';
    }
    
    /**
     * Setea el valor de un parametro en GET, POST y REQUEST
     * @param type $key Clave a setear
     * @param type $parameter Valor a establecer
     */
    public static function setParameter($key, $parameter){
        $_GET[$key] = $parameter;
        $_POST[$key] = $parameter;
        $_REQUEST[$key] = $parameter;
    }
    
    /**
     * Retorna la URL actual
     * @return string
     */
    public static function getSelf(){
        return $_SERVER["PHP_SELF"];
    }
    
    /**
     * Retorna la URL actual con los parametros entity, mainmenu y submenu, de la peticion anterior
     * @return string
     */
    public static function getSelfFull($paramsOptions = array()){
        $paramsUrl = array(
                        'id',
                        'rowid',
                        'mainmenu',
                        'submenu',
                        'entity'
                );
        
        $params = array();
        
        foreach ($paramsUrl as $param) {
            if(isset($paramsOptions[$param])){
                $params[$param] = $paramsOptions[$param];
            }else{
                $params[$param] = true;
            }
        }
        
        $self = '';
        $self.= self::getSelf().'?';
        $i = 0;
        $separator = '';
        
        foreach ($params as $key => $show) {
            if(self::isDefined($key) && $show){
                
                if($i > 0){
                    $separator = '&';
                }
                
                $self.= $separator.$key.'='.Request::getParameter($key);
                $i++;
            }
        }
        
        return $self;
    }
    
    /**
     * Retorna la URL de la peticion anterior
     * @return string
     */
    public static function getBefore(){
        return $_SERVER['HTTP_REFERER'];
    }
    
    /**
     * Verifica si existe un parametro definido que fue pasado por GET o POST
     * @param string $parameter
     * @return boolean
     */
    public static function isDefined($parameter){
        return isset($_REQUEST[$parameter]);
    }
    
    public static function isEmpty($parameter){
         if(isset($_REQUEST[$parameter])){
            return empty($_REQUEST[$parameter]);
        }else{
            return true;
         }
    }

    /**
     * Devuelve el tipo de metodo por el cual enviaron los parametros
     * @return boolean
     */
    public static function isMethodGet() {
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            return true;
        }
        else{
            return false;
        }
    }
    
    public static function deleteParameter($parameter){
        unset($_GET[$parameter]);
        unset($_POST[$parameter]);
        unset($_REQUEST[$parameter]);
    }
    
    /**
     * Devuelve el tipo de metodo por el cual enviaron los parametros
     * @return boolean
     */
    public static function isMethodPost() {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * Enlaza los datos enviados en la peticion anterior con un objeto pasado por parametro
     * @param object $object
     */
    public static function bindRequest(&$object,$formName = null){
        if(is_object($object)){
            $keys = get_object_vars($object);
            $keys = array_keys($keys);
            if($formName && self::isDefined($formName)){
                $dataForm = self::getParameter($formName);
                foreach($keys as $key){
                    if(isset($dataForm[$key]) && $key!='rowid'){
                        if(is_array($dataForm[$key]) == false){
                            $object->$key = Date::convertToDateTime($dataForm[$key]);
                        }elseif(self::isDefined($key)){
                            $object->$key = $dataForm[$key];
                        }
                    }
                }
            }else{
                foreach($keys as $key){
                    if($key=='rowid'){
                        continue;
                    }
                    if(self::isDefined($key) && !is_array(self::getParameter($key))){
                        $object->$key = Date::convertToDateTime(self::getParameter($key));
                    }elseif(self::isDefined($key)){
                        $object->$key = self::getParameter($key);
                    }
                }
            }
        }
    }

    /**
     * Retorna el token del formulario
     * @return string
     */
    public static function getFormToken() {
        $_SESSION['token'] = $_SESSION['newtoken'];
        return $_SESSION['newtoken'];
    }

    /**
     * Retorna la accion realizada por un formulario (el campo "action")
     * @return string
     */
    public static function getFormAction() {
        if(self::isDefined('action')){
            return Request::getParameter('action');
        }
        else{
            return NULL;
        }
    }
    
    /**
     * Limpia el parametro que recibe
     * @param string El parametro a limpiar
     * @return string El parametro limpio
     */
    protected function clearParameters($param){
        //  TODO Optimizar funcion
        //  TODO Limpiar de ataque de sql inyection
        return addslashes($param);
    }
    
    /**
    * Verifica si el formulario fue reenviado<br/>
    * <b>Retorna TRUE si el formulario es valido, sino false si es reenviado</b>
    * @return boolean
    */
    public static function actionBlock() {
        // TODO Hay que verificar que el token se valide
        if(Conf::ENVIRONMENT_DEVELOPMENT){
            $debug = '';
            
            if(self::isDefined('token')){
                $debug.= 'Action Block Success: ';
                $debug.= self::getParameter('token');
                Tools::debug($debug);
            }else{
                $debug.= 'Action Block Fail: Token Not Defined ';
                Notice::alert($debug, Notice::TYPE_ALERT_ERROR);
            }
            return true;
        }
        
        if(isset($_SESSION['oldtoken'])) {
            if (self::getParameter('token') == $_SESSION['oldtoken']) {
                return false;
            } else if (self::isDefined('token')){
                $_SESSION['oldtoken'] = self::getParameter('token');
                return true;
            }else{
                return false;
            }
        }else{
            $_SESSION['oldtoken'] = self::getParameter('token');
            return true;
        }    
    }
}
