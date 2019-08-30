# Path

Path is a very simple library for registering filesystem path shortcuts.

## Installation

Path can be installed with composer:

```sh
composer require simoneddy/path
```

## Example

This repository includes an example.php file with very basic examples:

```php
require 'vendor/autoload.php'; // Or wherever the autoloader is

// you can pass the root directory to the constructor
$path = new Eddy\Path\Path(__DIR__);

// or Path will attempt to locate it
$path = new Eddy\Path\Path(); // looks for vendor/autoload.php AND composer.json

// register a path with an optional shortcut
$path->register(dirname(__DIR__), 'parent');

var_dump($path->get('parent')); // equal to dirname(__DIR__)

var_dump($path->real('parent')); // equal to realpath(dirname(__DIR__))

// Both the get and real methods return the root path if no arguments are provided.
var_dump($path->get()); // equal to __DIR__
var_dump($path->real()); // equal to realpath(__DIR__)

// Both methods can also be used to resolve paths relative to the root dir, without
// being registered as shortcuts:
var_dump($path->get('vendor/autoload.php')); // "vendor/autoload.php"

```

## Magic Methods

The Path class contains several magic methods as a convenience:

```php
// Path contains a magic __toString method, which also returns the root path.
var_dump("The root directory is {$path}");

// Also provided are magic __get and __set methods for setting shortcuts like properties.
$path->home = $_SERVER['HOME']; // Set to the users home dir for something different.
var_dump($path->home); // returns equal to $_SERVER['HOME'];

// Finally, Path can be invoked. The magic __invoke method simply wraps the get()
// or real() methods.
var_dump($path('vendor/autoload.php'));
```

By default, magic methods will wrap the `get()` method. You can change the default behaviour to always providing `realpath` by either:

- Passing `true` as the second argument for Path's constructor,
- Using the `defaultToReal()` method, which accepts either true or false, or acts as a toggle between the two.

```php
// Setting defaultToReal to true in the constructor:
$path = new Eddy\Path\Path(__DIR__, true);

// Setting defaultToReal to true with the defaultToReal() method:
$path->defaultToReal(true);

// Disabling defaultToReal can be done by passing false, or by calling the method
// again while defaultToReal is enabled.
$path->defaultToReal(false);

```

## Todo

- add comments...
- add the ability to resolve paths relative to shortcuts.
