<?php

namespace Routing\DependencyInjection;

/**
 * Description of Container
 * Contains all services declared for dependency injection
 */
class Container
{
    protected $services;
    
    public function __construct()
    {
        $this->services = array();
        
        $this->set('service_container', $this);
    }

    public function set($id, $service)
    {
        $id = strtolower($id);

        $this->services[$id] = $service;
    }

    
    public function has($id)
    {
        $id = strtolower($id);

        return isset($this->services[$id]) || method_exists($this, 'get'.strtr($id, array('_' => '', '.' => '_')).'Service');
    }

    
    public function get($id)
    {
        $id = strtolower($id);

        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        if (isset($this->loading[$id])) {
            throw new ServiceCircularReferenceException($id, array_keys($this->loading));
        }

        if (method_exists($this, $method = 'get'.strtr($id, array('_' => '', '.' => '_')).'Service')) {
            $this->loading[$id] = true;

            try {
                $service = $this->$method();
            } catch (\Exception $e) {
                unset($this->loading[$id]);

                if (isset($this->services[$id])) {
                    unset($this->services[$id]);
                }

                throw $e;
            }

            unset($this->loading[$id]);

            return $service;
        }
        
            throw new ServiceNotFoundException($id);
    }

    
    public function initialized($id)
    {
        return isset($this->services[strtolower($id)]);
    }

    
    public function getServiceIds()
    {
        $ids = array();
        $r = new \ReflectionClass($this);
        foreach ($r->getMethods() as $method) {
            if (preg_match('/^get(.+)Service$/', $method->name, $match)) {
                $ids[] = self::underscore($match[1]);
            }
        }

        return array_unique(array_merge($ids, array_keys($this->services)));
    }
}

?>
