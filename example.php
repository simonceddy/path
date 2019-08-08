<?php
require 'vendor/autoload.php'; // require autoloader

// you can pass the root directory to the constructor
$path = new Eddy\Path\Path(__DIR__);

// or Path will attempt to locate it
$path = new Eddy\Path\Path(); // looks for vendor/autoload.php AND composer.json

// register a shortcut
$path->register(dirname(__DIR__), 'parent');

var_dump($path->get('parent')); // equal to dirname(__DIR__)

var_dump($path->real('parent')); // equal to realpath(dirname(__DIR__))

// Both the get and real methods return the root path if no arguments are provided.
var_dump($path->get()); // equal to __DIR__
var_dump($path->real()); // equal to realpath(__DIR__)

// Path contains a magic __toString method, which also returns the root path.
var_dump("The root directory is {$path}");

// Also provided are magic __get and __set methods for setting shortcuts like properties.
$path->vendor = 'vendor';
var_dump($path->real('vendor')); // returns equal to realpath(__DIR__ . '/vendor');
