<?php
namespace Eddy\Path;

use Eddy\Path\Support\RootDirLocator;

/**
 * Simple PHP library for managing relative filepaths and shortcuts.
 * 
 * @todo Split up code a little, spread responsibilities.
 * 
 * @package Path
 * @author Simon Eddy <simon@simoneddy.com.au>
 * @link https://github.com/simonceddy/path
 * @license MIT
 */
class Path
{
    /**
     * The path to the root directory
     *
     * @var string
     */
    protected $rootDir;

    /**
     * Registered paths and shortcuts
     *
     * @var string[]|Path[]
     */
    protected $paths = [];

    /**
     * Whether to use realpath for magic method access.
     * 
     * Defaults to false.
     *
     * @var bool
     */
    protected $defaultToReal;

    /**
     * The symbol to use as the Directory Separator.
     *
     * @var string
     */
    protected $delimiter;

    /**
     * Whether to store paths as strings or instances of Path.
     * 
     * Defaults to false.
     * 
     * @todo Work in progress - currently does nothing
     *
     * @var bool
     */
    protected $pathInstances;

    /**
     * Create a new Path instance.
     *
     * @param string|null $rootDir The application root directory, auto resolved if null.
     * @param bool|false $defaultToReal If true, magic methods will always return realpath.
     */
    public function __construct(string $rootDir = null, array $settings = [])
    {
        $this->loadInitialSettings($settings);
        if (!is_dir($rootDir)) {
            $rootDir = (new RootDirLocator)->locate();
        }
        $this->rootDir = $rootDir;
    }

    private function loadInitialSettings(array $settings)
    {
        $this->defaultToReal = isset(
            $settings['defaultToReal']
        ) && is_bool(
            $v = $settings['defaultToReal']
        ) ? $v : false;

        $this->pathInstances = isset(
            $settings['pathInstances']
        ) && is_bool(
            $v = $settings['pathInstances']
        ) ? $v : false;
        
        $this->delimiter = isset(
            $settings['delimiter']
        ) && is_string(
            $v = $settings['delimiter']
        ) ? $v : DIRECTORY_SEPARATOR;
    }

    /**
     * Internal method to return a valid path as a string.
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

    private function locateRelativeTo(string $path)
    {
        if (!strpos($path, DIRECTORY_SEPARATOR)) {
            return false;
        }

        $bits = explode(DIRECTORY_SEPARATOR, $path);

        $locating = array_shift($bits);

        $resolved = false;

        foreach ($bits as $bit) {
            if ($this->isRegistered($locating)) {
                $resolved = $this->get($locating);
                break;
            }
            $locating .= DIRECTORY_SEPARATOR . array_shift($bits);
        }

        if (false !== $resolved && !empty($bits)) {
            $resolved = $this->getValidPath(
                $resolved . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $bits)
            );
        }
    
        return $resolved;
    }

    private function transformDelimiter(string $path)
    {
        return str_replace($this->delimiter, DIRECTORY_SEPARATOR, $path);
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
    public function isRegistered(string $path)
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

        if (DIRECTORY_SEPARATOR !== $this->delimiter) {
            $path = $this->transformDelimiter($path);
        }

        if ($this->isRegistered($path)) {
            return $this->paths[$path];
        }

        $resolved = $this->getValidPath($path);
        
        if (!$resolved
            && !($resolved = $this->locateRelativeTo($path))
        ) {
            throw new PathException("Could not locate {$path}.");
        }

        return $resolved;
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
    public function register(string $path, string $filepath = null)
    {
        null !== $filepath ?: $filepath = $path;

        if (DIRECTORY_SEPARATOR !== $this->delimiter) {
            $filepath = $this->transformDelimiter($filepath);
            $path = $this->transformDelimiter($path);
        }

        $resolved = $this->getValidPath($filepath);
        
        if (!$resolved) {
            throw new PathException("Could not locate {$filepath}.");
        }

        $this->paths[$path] = $resolved;

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
        return $this->register($shortcut, $path);
    }

    public function __invoke(string $path = null)
    {
        return $this->defaultToReal ? $this->real($path) : $this->get($path);
    }

    public function __call(string $path, array $args = [])
    {
        if (isset($args[0]) && is_string($args[0])) {
            $path .= DIRECTORY_SEPARATOR . $args[0];
        }
        return $this->defaultToReal ? $this->real($path) : $this->get($path);
    }

    /**
     * Set if magic methods should always return realpath.
     * 
     * Applies only to __get, __invoke and __toString.
     *
     * @param bool|true $defaultToReal
     * 
     * @return  self
     */ 
    public function defaultToReal(bool $defaultToReal = true)
    {
        $this->defaultToReal = $defaultToReal;

        return $this;
    }

    public function makePath(string $path, string $filepath = null)
    {
        null !== $filepath ?: $filepath = $path;
        $newPath = $this->factory($filepath);
        $this->paths[$path] = $newPath;
        return $this->paths[$path];
    }

    public function factory(string $path)
    {
        try {
            $newPath = new static($this->real($path));
            return $newPath;
        } catch (PathException $e) {
            throw $e;
        }
    }
}
