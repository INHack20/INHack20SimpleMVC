<?php

namespace Routing;

use Routing\Loader\FileLoader;

/**
 * A FileController describes a route and its parameters.
 *
 */
class FileController extends FileLoader{
    private $name;
    private $path;
    
    function __construct($name,$path) {
        $this->name = $name;
        $this->path = $this->locate($path);
    }

    
    public function load()
    {
        $this->setCurrentDir(dirname($this->path));
        $controller = include $this->path;
        return $controller;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getFullName() {
        return $this->name.'Controller';
    }
}

?>
