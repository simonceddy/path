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

    function it_can_register_a_shortciut()
    {
        $this->set('spec', 'tests/spec/PathSpec.php');
        $this->get('spec')->shouldReturn(dirname(__DIR__, 2) . '/tests/spec/PathSpec.php');
    }

    function it_can_check_if_a_shortcut_is_set()
    {
        $this->set('spec', 'tests/spec/PathSpec.php');
        $this->has('spec')->shouldReturn(true);
    }

    function it_can_check_if_a_path_exists_relative_to_the_root_path()
    {
        $this->exists('composer.json')->shouldReturn(true);
    }

    function it_can_unregister_a_shortcut()
    {
        $this->set('spec', 'tests/spec/PathSpec.php');
        $this->has('spec')->shouldReturn(true);
        $this->remove('spec');
        $this->has('spec')->shouldReturn(false);
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
