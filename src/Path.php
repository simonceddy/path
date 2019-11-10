<?php
namespace Eddy\Path;

class Path implements \ArrayAccess
{
    /**
     * The root path
     *
     * @var string
     */
    protected $rootDir;

    /**
     * An array of shortcuts
     *
     * @var array
     */
    protected $shortcuts;

    /**
     * Construct a new Path instance.
     * 
     * The first argument passed to the constructor must be the Path's root
     * directory. For example, this might be your project's root folder.
     *
     * The second argument is an optional array of shortcuts to register on
     * construction. Entries should be key value pairs where shortcut is the
     * key and its path is the value. Invalid entries will be ignored.
     * 
     * @param string $rootDir
     * @param array $shortcuts
     */
    public function __construct(string $rootDir, array $shortcuts = [])
    {
        $this->rootDir = $rootDir;

        if (!empty($shortcuts)) {
            $this->shortcuts = array_filter(array_map(function ($path) {
                return $this->getFullPath($path);
            }, $shortcuts));
        }
    }

    /**
     * Returns the realpath for $path, or the path relative to the root dir.
     * 
     * Returns false if no path is resolved.
     *
     * @param string $path
     *
     * @return string|false
     */
    protected function getFullPath(string $path)
    {
        if (file_exists($path)) {
            return realpath($path);
        }
        if (file_exists(
            $this->rootDir . DIRECTORY_SEPARATOR . $path
        )) {
            return $this->rootDir . DIRECTORY_SEPARATOR . $path;
        }
        return false;
    }

    /**
     * Check if the given path exists.
     * 
     * This method does not check shortcuts. To include shortcuts in the check
     * use the has() method.
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists(string $path)
    {
        return $this->getFullPath($path) !== false;
    }

    /**
     * Check if a shortcut is registered or a given path exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has(string $path)
    {
        return isset($this->shortcuts[$path]) ?? $this->exists($path);
    }

    /**
     * Register a shortcut
     *
     * @param string $shortcut
     * @param string $path
     *
     * @return self
     */
    public function set(string $shortcut, string $path)
    {
        if (!$resolvedPath = $this->getFullPath($path)) {
            throw new PathException(
                "Could not find {$path}."
            );
        }

        $this->shortcuts[$shortcut] = $resolvedPath;

        return $this;
    }

    /**
     * Returns the full path for $path.
     * 
     * $path can be a registered shortcut, a path relative to the root dir, or
     * an already resolvable path (because why not).
     * 
     * If $path is null the root directory path will be returned.
     *
     * @param string|null $path
     *
     * @return string
     * 
     * @throws PathException Throws if no path is found.
     */
    public function get(string $path = null)
    {
        if ($path === null) {
            return $this->rootDir;
        }

        if (isset($this->shortcuts[$path])) {
            return $this->shortcuts[$path];
        }

        if (!$resolvedPath = $this->getFullPath($path)) {
            throw new PathException(
                "Could not find {$path}."
            );
        }

        return $resolvedPath;
    }

    /**
     * Remove a shortcut.
     * 
     * This method only unregisters a shortcut and has no effect on the
     * filesystem.
     *
     * @param string $name
     *
     * @return self
     */
    public function remove($name)
    {
        unset($this->shortcuts[$name]);
        return $this;
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Magic getter for shortcuts and paths.
     * 
     * Sends call to get() method.
     *
     * @param string $path
     *
     * @return string
     */
    public function __get(string $path)
    {
        return $this->get($path);
    }

    /**
     * Magic setter for shortcuts.
     * 
     * Sends call to set() method.
     *
     * @param string $shortcut
     * @param string $path
     */
    public function __set(string $shortcut, string $path)
    {
        $this->set($shortcut, $path);
    }

    /**
     * Returns the root directory path.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->rootDir;
    }
}
