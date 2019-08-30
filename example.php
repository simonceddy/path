<?php
require 'vendor/autoload.php'; // require autoloader

// you can pass the root directory to the constructor
$path = new Eddy\Path\Path(__DIR__, true);

// or Path will attempt to locate it
$path = new Eddy\Path\Path(); // looks for vendor/autoload.php AND composer.json

// register a shortcut
$path->register(dirname(__DIR__), 'parent');

var_dump($path->get('parent')); // equal to dirname(__DIR__)

var_dump($path->real('parent')); // equal to realpath(dirname(__DIR__))

// Both the get and real methods return the root path if no arguments are provided.
var_dump($path->get()); // equal to __DIR__
var_dump($path->real()); // equal to realpath(__DIR__)

// Both can also be used to resolve paths relative to the root dir, without
// being registered as shortcuts:
var_dump($path->get('vendor/autoload.php')); // "vendor/autoload.php"

// Path contains a magic __toString method, which also returns the root path.
var_dump("The root directory is {$path}");

// Also provided are magic __get and __set methods for setting shortcuts like properties.
$path->home = $_SERVER['HOME']; // Set to the users home dir for something different.
var_dump($path->real('home')); // returns equal to realpath($_SERVER['HOME']);

// Finally, Path can also be invoked.
// Invoking without an argument will return the root directory.
// Invoking with an argument will act identically to get().
var_dump($path('vendor/autoload.php'));
