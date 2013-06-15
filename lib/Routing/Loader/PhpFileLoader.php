<?php
namespace Routing\Loader;

use Routing\Resource\FileResource;

/**
 * PhpFileLoader loads routes from a PHP file.
 *
 * The file must return a RouteCollection instance.
 * 
 * @api
 */
class PhpFileLoader extends FileLoader
{
    private $currentDir;

    /**
     * Loads a PHP file.
     *
     * @param mixed  $file A PHP file path
     * @param string $type The resource type
     *
     * @api
     */
    public function load($file, $type = null)
    {
        // the loader variable is exposed to the included file below
        $loader = $this;
        $path = $this->locate($file);
        $this->setCurrentDir(dirname($path));

        $collection = include $path;
        $collection->addResource(new FileResource($path));

        return $collection;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'php' === $type);
    }
    
    public function getCurrentDir() {
        return $this->currentDir;
    }

    public function setCurrentDir($currentDir) {
        $this->currentDir = $currentDir;
    }
    
    public function locate($name, $currentPath = null, $first = true)
    {
        if ($this->isAbsolutePath($name)) {
            if (!file_exists($name)) {
                throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $name));
            }

            return $name;
        }
        if(file_exists(BASE_PATH_CONTROLLER.$name)){
            return BASE_PATH_CONTROLLER.$name;
        }
        if (file_exists($name)) {
            return $name;
        }else{
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $name));
        }
    }
    
    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     *
     * @return Boolean
     */
    private function isAbsolutePath($file)
    {
        if ($file[0] == '/' || $file[0] == '\\'
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] == ':'
                && ($file[2] == '\\' || $file[2] == '/')
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }
}
