<?php
namespace Eddy\Path;

class Path
{
    protected $rootDir;

    protected $paths = [];

    protected $defaultToReal;

    /**
     * Create a new Path instance.
     *
     * @param string $rootDir The application root directory, auto resolved if null.
     * @param bool $defaultToReal If true, magic methods will always return realpath.
     */
    public function __construct(string $rootDir = null, bool $defaultToReal = false)
    {
        $this->defaultToReal = $defaultToReal;
        if (!is_dir($rootDir)) {
            $rootDir = (new RootDirLocator)->locate();
        }
        $this->rootDir = $rootDir;
    }

    /**
     * Internal method to return a valid path as a string.
     *
     * @internal Used for validating the existence of a path.
     * 
     * @param string $path
     *
     * @return string|false
     */
    private function getValidPath(string $path)
    {
        if (!file_exists($path)
            && !file_exists($path = $this->rootDir . DIRECTORY_SEPARATOR . $path)
        ) {
            return false;
        }

        return $path;
    }

    /**
     * Checks if a path is registered.
     * 
     * NOTE This does not check if a path exists, only if registered.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has(string $path)
    {
        return isset($this->paths[$path])
            || in_array($path, $this->paths);
    }

    /**
     * Returns the resolved path. If no argument is given it will return the root directory.
     *
     * @param string|null $path Can be a path relative to the root dir or a registered shortcut.
     *
     * @return string;
     * 
     * @throws PathException Thrown if the requested path cannot be resolved.
     */
    public function get(string $path = null)
    {
        if (null === $path) {
            return $this->rootDir;
        }

        if ($this->has($path)) {
            return $this->paths[$path];
        }

        $path = $this->getValidPath($path);
        
        if (!$path) {
            throw new PathException("Could not locate {$path}.");
        }

        return $path;
    }

    /**
     * Register a path with an optional shortcut.
     *
     * @param string $path
     * @param string $shortcut
     *
     * @return self
     * 
     * @throws PathException
     */
    public function register(string $path, string $shortcut = null)
    {
        $path = $this->getValidPath($path);
        
        if (!$path) {
            throw new PathException("Could not locate {$path}.");
        }

        $this->paths[$shortcut ?? $path] = $path;

        return $this;
    }

    /**
     * Returns the realpath value of get($path).
     *
     * @param string $path
     *
     * @return string
     */
    public function real(string $path = null)
    {
        return realpath($this->get($path));
    }

    /**
     * Magic toString method to allow using the Path instance in a string.
     * 
     * Returns the root directory. If defaultToReal is true it will return the
     * root directory's realpath.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->defaultToReal ? $this->real() : $this->get();
    }

    /**
     * Magic get method to allow accessing paths as public properties.
     * 
     * If defaultToReal is true it will return the path's realpath.
     *
     * @return string
     */
    public function __get(string $path)
    {
        return $this->defaultToReal ? $this->real($path) : $this->get($path);
    }

    /**
     * Magic get method to allow settings paths as public properties.
     * 
     * Wraps the register() method and functions identically.
     * 
     * @return string
     */
    public function __set(string $shortcut, string $path)
    {
        return $this->register($path, $shortcut);
    }

    public function __invoke(string $path = null)
    {
        return $this->defaultToReal ? $this->real($path) : $this->get($path);
    }

    /**
     * Set if magic methods should always return realpath.
     * 
     * Applies only to __get, __invoke and __toString.
     * 
     * If no argument is supplied, this method will act as a toggle, with each
     * call inverting the previous setting (e.g. true to false, visa versa).
     * 
     * Supplying a boolean argument will use that boolean, regardless of the
     * current settings.
     *
     * @param bool|null $defaultToReal
     * 
     * @return  self
     */ 
    public function defaultToReal(bool $defaultToReal = null)
    {
        $this->defaultToReal = $defaultToReal ?? !$this->defaultToReal;

        return $this;
    }
}
