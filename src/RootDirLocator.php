<?php
namespace Eddy\Path;

class RootDirLocator
{
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
