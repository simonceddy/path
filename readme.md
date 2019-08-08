# Path

Path is a very simple library for registering filesystem path shortcuts.

## Use

```php
require 'vendor/autoload.php'; // Or wherever the autoloader is

// you can pass the root directory to the constructor
$path = new Eddy\Path\Path(__DIR__);

// or Path will attempt to locate it
$path = new Eddy\Path\Path(); // looks for vendor/autoload.php AND composer.json

```
