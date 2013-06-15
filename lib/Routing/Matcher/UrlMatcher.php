<?php

namespace Routing\Matcher;

use InvalidArgumentException;
use ReflectionClass;
use Routing\DependencyInjection\Container;
use Routing\Exception\ControllerNotFoundException;
use Routing\Exception\InvalidParameterException;
use Routing\Exception\MethodNotAllowedException;
use Routing\Exception\MethodReturnInvalidValueException;
use Routing\Exception\MissingMandatoryParametersException;
use Routing\Exception\RouteNotFoundException;
use Routing\FileControllerCollection;
use Routing\RouteCollection;
use Routing\Generator\UrlGenerator;

/**
 * UrlMatcher matches URL based on a set of routes.
 *
 * @api
 */
class UrlMatcher {

    protected $controllers;
    private $routes;
    private $container;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes  A RouteCollection instance
     * @param FileControllerCollection  $controllers The context
     *
     * @api
     */
    public function __construct(RouteCollection $routes, FileControllerCollection $controllers, Container $container = null) {
        $this->routes = $routes;
        $this->controllers = $controllers;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo) {
        if (isset($_REQUEST[UrlGenerator::ROUTE])) {
            $name = $_REQUEST[UrlGenerator::ROUTE];
            unset($_REQUEST[UrlGenerator::ROUTE]);
            if (empty($name)) {
                throw new InvalidParameterException('route parameter can not be empty');
            }
            if (null === $route = $this->routes->get($name)) {
                throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
            }
            if (!$route->isValidOptions()) {
                throw new RouteNotFoundException(sprintf('Route "%s" is not valid.', $name));
            }
            foreach ($route->getRequirements() as $requirement) {
                if (!isset($_REQUEST[$requirement])) {
                    throw new MissingMandatoryParametersException(sprintf('The "%s" route has some missing mandatory parameters ("%s").', $name, $requirement));
                } elseif (empty($_REQUEST[$requirement])) {
                    throw new InvalidArgumentException(sprintf('mandatory parameter "%s" can not be empty.', $requirement));
                }
            }

            if (null === $controller = $this->controllers->get($route->getController())) {
                throw new ControllerNotFoundException(sprintf('File Controller "%s" does not exist.', $route->getController()));
            }

            $controller->load();

            if (class_exists($controller->getFullName())) {
                $class = $controller->getFullName();
                $objectController = new $class;
                $methodAction = $route->getFullMethod();
                $reflection = new ReflectionClass($objectController);
                $allowedMethods = array();
                foreach ($reflection->getMethods() as $method) {
                    if ($method->class === $class) {
                        $allowedMethods [] = $method->name;
                    }
                }

                if (method_exists($objectController, 'setContainer')) {
                    $objectController->setContainer($this->container);
                }

                if (method_exists($objectController, $methodAction)) {
                    $render = $objectController->$methodAction();
                    if ($render === NULL) {
                        throw new MethodReturnInvalidValueException(sprintf('The controller method "%s->%s()" must return a valid value, returned "%s"', $controller->getFullName(), $methodAction, 'NULL'));
                    }
                    if (empty($render)) {
                        throw new MethodReturnInvalidValueException(sprintf('The controller method "%s->%s()" must return a valid value, returned "empty value"', $controller->getFullName(), $methodAction));
                    }
                    echo $render;
                    exit();
                } else {
                    throw new MethodNotAllowedException($allowedMethods, sprintf('Method "%s" not available in class "%s"', $methodAction, $reflection->getName()));
                }
            } else {
                throw new ControllerNotFoundException(sprintf('Class %s does not exist', $controller->getFullName()));
            }
        }
    }

}
