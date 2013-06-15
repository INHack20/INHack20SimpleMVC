<?php
require_once 'Loader/Loader.php';
use Loader\Loader;

$loader = new Loader();
$loader->loadBasePrefixes();
$loader->register();

$baseDir = dirname(__DIR__);
define('DS',DIRECTORY_SEPARATOR);
define('BASE_PATH_CONTROLLER', $baseDir.DS.'src'.DS.'Controller'.DS);
define('BASE_PATH_TPL', $baseDir.DS.'src'.DS.'Views'.DS);

require_once $baseDir.'/app/config/controllers.php';//Cargo los controladores definidos
require_once $baseDir.'/app/config/routing.php';//Cargo las rutas definidas
require_once $baseDir.'/app/config/services.php';//Cargo los servicios del contenedor
