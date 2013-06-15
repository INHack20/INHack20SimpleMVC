<?php

require_once (__DIR__.'/../lib/autoload.php');

use Routing\RequestContext;
use Routing\Router;
use Core\Template;

$requestContext = new RequestContext();
$router = new Router($routeCollection, $fileControllerCollection,$requestContext);
$router->setContainer($container);

$template = new Template();

$container->set('router',$router);
$container->set('template',$template);

$router->match($_SERVER['QUERY_STRING']);