<?php
namespace Routing\DependencyInjection;
/**
 * A simple implementation of ContainerAware
 *
 * @api
 */
abstract class ContainerAware
{
    /**
     * @var ContainerInterface
     *
     * @api
     */
    protected $container;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(Container $container = null)
    {
        $this->container = $container;
    }
}
