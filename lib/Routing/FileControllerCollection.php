<?php
namespace Routing;
/**
 * A FileControllerCollection represents a set of FileController instances as a tree structure.
 *
 * When adding a route, it overrides existing controllers with the
 * same name defined in the instance or its children and parents.
 *
 */
class FileControllerCollection {
    
    private $controllers;
    private $parent;
    
    /**
     * Constructor.
     *
     * @api
     */
    public function __construct()
    {
        $this->controllers = array();
    }

    public function __clone()
    {
        foreach ($this->controllers as $name => $fileController) {
            $this->controllers[$name] = clone $fileController;
            if ($fileController instanceof FileControllerCollection) {
                $this->controllers[$name]->setParent($this);
            }
        }
    }

    /**
     * Gets the parent FileControllerCollection.
     *
     * @return FileControllerCollection|null The parent FileControllerCollection or null when it's the root
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Gets the root FileControllerCollection of the tree.
     *
     * @return FileControllerCollection The root FileControllerCollection
     */
    public function getRoot()
    {
        $parent = $this;
        while ($parent->getParent()) {
            $parent = $parent->getParent();
        }

        return $parent;
    }

    /**
     * Gets the current FileControllerCollection as an Iterator that lib all controllers and child route collections.
     *
     * @return \ArrayIterator An \ArrayIterator interface
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->controllers);
    }

    /**
     * Gets the number of Routes in this collection.
     *
     * @return int The number of controllers in this collection, including nested collections
     */
    public function count()
    {
        $count = 0;
        foreach ($this->controllers as $fileController) {
            $count += $fileController instanceof FileControllerCollection ? count($fileController) : 1;
        }

        return $count;
    }

    /**
     * Adds a route.
     *
     * @param string $name  The route name
     * @param FileController  $fileController A FileController instance
     *
     * @throws \InvalidArgumentException When route name contains non valid characters
     *
     * @api
     */
    public function add(FileController $fileController)
    {
        $name = $fileController->getName();
        if (!preg_match('/^[a-z0-9A-Z_.]+$/', $name)) {
            throw new \InvalidArgumentException(sprintf('The provided route name "%s" contains non valid characters. A route name must only contain digits (0-9), letters (a-z and A-Z), underscores (_) and dots (.).', $name));
        }

        $this->remove($name);

        $this->controllers[$name] = $fileController;
    }

    /**
     * Returns all controllers in this collection and its children.
     *
     * @return array An array of controllers
     */
    public function all()
    {
        $controllers = array();
        foreach ($this->controllers as $name => $fileController) {
            if ($fileController instanceof FileControllerCollection) {
                $controllers = array_merge($controllers, $fileController->all());
            } else {
                $controllers[$name] = $fileController;
            }
        }

        return $controllers;
    }

    /**
     * Gets a route by name defined in this collection or its children.
     *
     * @param string $name The route name
     *
     * @return FileController|null A FileController instance or null when not found
     */
    public function get($name)
    {
        if (isset($this->controllers[$name])) {
            return $this->controllers[$name] instanceof FileControllerCollection ? null : $this->controllers[$name];
        }

        foreach ($this->controllers as $controllers) {
            if ($controllers instanceof FileControllerCollection && null !== $fileController = $controllers->get($name)) {
                return $fileController;
            }
        }

        return null;
    }

    /**
     * Removes a route or an array of controllers by name from all connected
     * collections (this instance and all parents and children).
     *
     * @param string|array $name The route name or an array of route names
     */
    public function remove($name)
    {
        $root = $this->getRoot();

        foreach ((array) $name as $n) {
            $root->removeRecursively($n);
        }
    }

    /**
     * Adds a route collection to the current set of controllers (at the end of the current set).
     *
     * @param FileControllerCollection $collection   A FileControllerCollection instance
     * @param array           $defaults     An array of default values
     * @param array           $requirements An array of requirements
     * @param array           $options      An array of options
     *
     * @throws \InvalidArgumentException When the FileControllerCollection already exists in the tree
     *
     * @api
     */
    public function addCollection(FileControllerCollection $collection)
    {
        // prevent infinite loops by recursive referencing
        $root = $this->getRoot();
        if ($root === $collection || $root->hasCollection($collection)) {
            throw new \InvalidArgumentException('The FileControllerCollection already exists in the tree.');
        }

        // remove all controllers with the same names in all existing collections
        $this->remove(array_keys($collection->all()));

        $collection->setParent($this);
        // the sub-collection must have the prefix of the parent (current instance) prepended because it does not
        // necessarily already have it applied (depending on the order FileControllerCollections are added to each other)
        $this->controllers[] = $collection;
    }

    
    /**
     * Sets the parent FileControllerCollection. It's only used internally from one FileControllerCollection
     * to another. It makes no sense to be available as part of the public API.
     *
     * @param FileControllerCollection $parent The parent FileControllerCollection
     */
    private function setParent(FileControllerCollection $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Removes a route by name from this collection and its children recursively.
     *
     * @param string $name The route name
     *
     * @return Boolean true when found
     */
    private function removeRecursively($name)
    {
        // It is ensured by the adders (->add and ->addCollection) that there can
        // only be one route per name in all connected collections. So we can stop
        // iterating recursively on the first hit.
        if (isset($this->controllers[$name])) {
            unset($this->controllers[$name]);

            return true;
        }

        foreach ($this->controllers as $controllers) {
            if ($controllers instanceof FileControllerCollection && $controllers->removeRecursively($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether the given FileControllerCollection is already set in any child of the current instance.
     *
     * @param FileControllerCollection $collection A FileControllerCollection instance
     *
     * @return Boolean
     */
    private function hasCollection(FileControllerCollection $collection)
    {
        foreach ($this->controllers as $controllers) {
            if ($controllers === $collection || $controllers instanceof FileControllerCollection && $controllers->hasCollection($collection)) {
                return true;
            }
        }

        return false;
    }
    
}

?>
