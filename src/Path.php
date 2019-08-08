<?php
namespace Eddy\Path;

class Path
{
    protected $rootDir;

    protected $paths = [];

    public function __construct(string $rootDir = null)
    {
        if (!is_dir($rootDir)) {
            $rootDir = $this->locateRootDir();
        }
        $this->rootDir = $rootDir;
    }

    private function locateRootDir()
    {
        $dir = dirname(__DIR__);
        while ((!file_exists($dir . '/composer.json')
            || !file_exists($dir . '/vendor/autoload.php'))
            && $dir !== ($next = dirname($dir))
        ) {
            $dir = $next;
        }

        return $dir;
    }

    private function validatePath(string $path)
    {
        if (!file_exists($path)
            && !file_exists($path = $this->rootDir . DIRECTORY_SEPARATOR . $path)
        ) {
            throw new PathException("Could not locate {$path}.");
        }

        return $path;
    }

    public function has(string $path)
    {
        return isset($this->paths[$path])
            || in_array($path, $this->paths);
    }

    public function get(string $path = null)
    {
        if (null === $path) {
            return $this->rootDir;
        }

        if ($this->has($path)) {
            return $this->paths[$path];
        }

        return $this->validatePath($path);
    }

    public function register(string $path, string $shortcut = null)
    {
        $this->paths[$shortcut ?? $path] = $this->validatePath($path);

        return $this;
    }

    public function real(string $path = null)
    {
        return realpath($this->get($path));
    }

    public function __toString()
    {
        return $this->real();
    }

    public function __get(string $path)
    {
        return $this->real($path);
    }

    public function __set(string $shortcut, string $path)
    {
        return $this->register($path, $shortcut);
    }
}
