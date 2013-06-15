<?php
namespace Routing;

use Routing\FileControllerCollection;
use Routing\RequestContext;
use Routing\RouteCollection;
use Routing\DependencyInjection\ContainerAware;
use \InvalidArgumentException;

/**
 * The Router class is an example of the integration of all pieces of the
 * routing system for easier use.
 *
 */
class Router extends ContainerAware
{
    protected $matcher;
    protected $generator;
    protected $context;
    protected $collection;
    protected $options;
    protected $controllers;

    /**
     * Constructor.
     *
     * @param array           $options  An array of options
     * @param RequestContext  $context  The context
     */
    public function __construct(RouteCollection $collection , FileControllerCollection $controllers, RequestContext $context = null,array $options = array())
    {
        $this->collection = $collection;
        $this->controllers = $controllers;
        $this->context = null === $context ? new RequestContext() : $context;
        $this->setOptions($options);
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * cache_dir:     The cache directory (or null to disable caching)
     *   * debug:         Whether to enable debugging or not (false by default)
     *
     * @param array $options An array of options
     *
     * @throws InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        $this->options = array(
            'generator_class'        => 'Routing\Generator\UrlGenerator',
            'matcher_class'          => 'Routing\Matcher\UrlMatcher',
        );

        // check option names and live merge, if errors are encountered Exception will be thrown
        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new InvalidArgumentException(sprintf('The Router does not support the following options: "%s".', implode('\', \'', $invalid)));
        }
    }

    /**
     * Sets an option.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     *
     * @throws InvalidArgumentException
     */
    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }

    /**
     * Gets an option value.
     *
     * @param string $key The key
     *
     * @return mixed The value
     *
     * @throws InvalidArgumentException
     */
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        if (null !== $this->matcher) {
            $this->getMatcher()->setContext($context);
        }
        if (null !== $this->generator) {
            $this->getGenerator()->setContext($context);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        return $this->getMatcher()->match($pathinfo);
    }

    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcherInterface A UrlMatcherInterface instance
     */
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        return $this->matcher = new $this->options['matcher_class']($this->getRouteCollection(), $this->controllers, $this->container);
    }

    /**
     * Gets the UrlGenerator instance associated with this Router.
     *
     * @return UrlGeneratorInterface A UrlGeneratorInterface instance
     */
    public function getGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }

        $this->generator = new $this->options['generator_class']($this->getRouteCollection(), $this->context);
        
        return $this->generator;
    }

}
