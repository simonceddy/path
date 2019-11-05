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
        $this->register('spec', dirname(__FILE__));
        $this->get('spec')->shouldReturn(dirname(__FILE__));
    }

    function it_can_register_a_path_relative_to_root_dir()
    {
        $this->register('spec', 'tests/spec');
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

    function it_can_find_paths_relative_to_shortcuts()
    {
        $this->register('spec', 'tests/spec');
        $this->real('spec/PathSpec.php')->shouldBeEqualTo(realpath(__FILE__));
    }

    function it_can_toggle_realpath_for_magic_methods()
    {
        $this->beConstructedWith(dirname(__DIR__, 2), ['defaultToReal' => true]);
        $this->v = 'vendor';
        $this->v->shouldBeEqualTo(realpath(dirname(__DIR__, 2) . '/vendor'));

        $this->defaultToReal(false);
        $this->v->shouldBeEqualTo('vendor');
    }

    function it_can_set_a_custom_directory_separator()
    {
        $this->beConstructedWith(dirname(__DIR__, 2), ['delimiter' => '|']);
        $this->real('vendor|autoload.php')->shouldBeEqualTo(dirname(__DIR__, 2) . '/vendor/autoload.php');

        $this->register('composer', 'vendor|composer');
        $this->real('composer')->shouldBeEqualTo(dirname(__DIR__, 2) . '/vendor/composer');
    }
}
