<?php

use Routing\RouteCollection;
use Routing\Loader\PhpFileLoader;

$phpFile = new PhpFileLoader();
$routeCollection = new RouteCollection();
$routeCollection->addCollection($phpFile->load('Installer/routing.php'));