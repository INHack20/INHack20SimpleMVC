<?php
use Routing\RouteCollection;
use Routing\Route;

$collection = new RouteCollection();

$collection->add('installer_index', new Route('Installer:index'));
$collection->add('installer_step_one', new Route('Installer:stepOne'));
$collection->add('installer_step_two', new Route('Installer:stepTwo',array(),array('methods' => 'POST')));

return $collection;

?>
