<?php

namespace spec\Eddy\Path;

use Eddy\Path\Path;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PathSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(dirname(__DIR__, 2));
    }

    function it_has_a_root_directory()
    {
        $this->__toString()->shouldReturn(dirname(__DIR__, 2));
    }

    function it_can_be_constructed_with_an_array_of_shortcuts()
    {
        $this->beConstructedWith(dirname(__DIR__, 2), [
            'autoload' => 'vendor/autoload.php'
        ]);

        $this
            ->get('autoload')
            ->shouldReturn(dirname(__DIR__, 2) . '/vendor/autoload.php');
    }
}
