# Path

Path is a very simple library for registering filesystem path shortcuts.

## Installation

Path can be installed with composer:

```sh
composer require simoneddy/path
```

## Basic Example

```php
require 'vendor/autoload.php'; // Or wherever the autoloader is

// you can pass the root directory to the constructor
$path = new Eddy\Path\Path(__DIR__, [/* optional settings array */]);

// or Path will attempt to locate it
$path = new Eddy\Path\Path(); // looks for vendor/autoload.php AND composer.json

// register a path with an optional shortcut
$path->register('parent', dirname(__DIR__));

var_dump($path->get('parent')); // equal to dirname(__DIR__)

var_dump($path->real('parent')); // equal to realpath(dirname(__DIR__))

// Both the get and real methods return the root path if no arguments are provided.
var_dump($path->get()); // equal to __DIR__
var_dump($path->real()); // equal to realpath(__DIR__)

// Both methods can also be used to resolve paths relative to the root dir, without
// being registered as shortcuts:
var_dump($path->get('vendor/autoload.php')); // "vendor/autoload.php"

// Path can also resolve file and directory paths relative to shortcuts:
$path->register('home', $_SERVER['HOME']);
var_dump($path->get('home/Documents')); // equal to $_SERVER['HOME'] . '/Documents'

```

## Magic Methods

The Path class contains several magic methods as a convenience:

```php
// Path contains a magic __toString method, which also returns the root path.
var_dump("The root directory is {$path}");

// Also provided are magic __get and __set methods for setting shortcuts like properties.
$path->home = $_SERVER['HOME']; // Set to the users home dir for something different.
var_dump($path->home); // returns equal to $_SERVER['HOME'];

// Path can also be invoked. The magic __invoke method simply wraps the get()
// or real() methods.
var_dump($path('vendor/autoload.php'));

// Finally, Path supports calls to registered paths:
var_dump($path->home()); // same as $path->get('home');

// The __call magic method handles a string argument as a relative path:
var_dump($path->home('Documents/dev')); // same as $path->get('home/Documents/dev')
```

By default, magic methods will wrap the `get()` method. You can change the default behaviour to always providing `realpath` by either:

- Having `'defaultToReal'` set to `true` in the optional settings array available as the constructors second argument,
- Using the `defaultToReal()` method, which accepts an optional boolean or defaults to `true`.

```php
// Setting defaultToReal to true in the constructor:
$path = new Eddy\Path\Path(__DIR__, ['defaultToReal' => true]);

// Setting defaultToReal to true with the defaultToReal() method:
$path->defaultToReal();

// Disabling defaultToReal can be done by passing false to the method:
$path->defaultToReal(false);

```

## Creating Paths from Paths

Every Path object has a `makePath()` method and a `factory()` method, which will attempt to create a new Path object from a given shortcut or filepath.

The `factory()` accepts a valid shortcut or path as a single argument and will simply return the newly created Path object.

The `makePath()` will also save the newly created object as a registered path. It can accept either one or two arguments and functions similarly to Path's `register()` method. The optional second argument specifies a valid filepath and causes the first argument to be used as a shortcut.

```php
$path = new Eddy\Path\Path(dirname(__DIR__, 2) . '/react');

// The factory() method creates a new Path instance with the given path as the new
// instances root directory and returns it straight to us:
$website = $path->factory('website'); // $website is a new instance of Path
// the root directory of the $website object is equal to dirname(__DIR__, 2) . '/react/website'

// We can create a new Path instance from a shortcut, or a path relative to a shortcut.
// The makePath() method will also store our new Path object and return it
// if we request the given path again:
$path->home = $_SERVER['HOME'];
$docs = $path->makePath('docs', 'home/Documents');

var_dump($path->docs); // Instance of Path
```

## Available Settings

The Path class constructor can be passed an optional array of settings as the second argument:

```php
// if auto resolving the root dir, simply pass null as the first argument
$path = new Path('path/to/root/dir/or/null', [
    // valid settings can overwrite some of Path's default behaviour
    'delimiter' => '||',
    'defaultToReal' => true
]);
```

The available settings are summarised below:

- boolean __defaultToReal__ (default is false) - If set to true, all magic getter methods will always return realpath. This setting can also be toggled with the `defaultToReal()` method.

- string __delimiter__ (default is the value of PHP's `DIRECTORY_SEPARATOR` constant) - Can be used to set a custom directory separator symbol. Use with caution as any delimiter symbols will take precedence over actual directory and file names.
