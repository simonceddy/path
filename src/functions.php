<?php
namespace Eddy\Path;

function locateProjectDir()
{
    $dir = __DIR__;
        while ((!file_exists($dir . '/composer.json')
            || !file_exists($dir . '/vendor/autoload.php'))
            && $dir !== ($next = dirname($dir))
        ) {
            $dir = $next;
        }

        return $dir;
}