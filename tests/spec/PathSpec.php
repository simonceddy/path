<?php

namespace spec\Eddy\Path;

use Eddy\Path\Path;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Eddy\Path\PathException;

class PathSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(dirname(__DIR__, 2));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Path::class);
    }

    function it_is_aware_of_the_project_root_dir()
    {
        $this->get()->shouldReturn(dirname(__DIR__, 2));
    }

    function it_can_locate_the_project_root_dir()
    {
        $this->beConstructedWith(null);
        $this->get()->shouldReturn(dirname(__DIR__, 2));
    }

    function it_can_register_a_path()
    {
        $this->register(dirname(__FILE__));
        $this->get(dirname(__FILE__))->shouldReturn(dirname(__FILE__));
    }

    function it_can_register_a_shortcut_to_a_path()
    {
        $this->register(dirname(__FILE__), 'spec');
        $this->get('spec')->shouldReturn(dirname(__FILE__));
    }

    function it_can_register_a_path_relative_to_root_dir()
    {
        $this->register('tests/spec', 'spec');
        $this->real('spec')->shouldReturn(dirname(__FILE__));
    }

    function it_can_be_used_as_root_dir_string()
    {
        $this->beConstructedWith(__DIR__);
        $this->__toString()->shouldReturn(__DIR__);
    }

    function it_has_magic_getter_and_setters()
    {
        $this->vendor = dirname(__DIR__, 2) . '/vendor';
        $this->vendor->shouldBeEqualTo(dirname(__DIR__, 2) . '/vendor');
        $this->get('vendor')->shouldBeEqualTo(dirname(__DIR__, 2) . '/vendor');
    }

    function it_throws_a_path_exception_for_invalid_path()
    {
        $this->shouldThrow(PathException::class)->duringRegister('unknown', 'broke');
    }
}
