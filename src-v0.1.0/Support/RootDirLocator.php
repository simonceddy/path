<?php
namespace Eddy\Path\Support;

/**
 * Simple class for locating a project's root directory.
 * 
 * @package Path
 * @author Simon Eddy <simon@simoneddy.com.au>
 * @link https://github.com/simonceddy/path
 * @license MIT
 */
class RootDirLocator
{
    /**
     * Attempt to automatically resolve a project's root directory.
     * 
     * Loops through parent directories searching for both composer.json and
     * vendor/autoload.php. If the loop reaches the top level directory it will
     * terminate and the top level directory path (usually '/') will be
     * returned.
     *
     * @return string
     */
    public function locate()
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
}
