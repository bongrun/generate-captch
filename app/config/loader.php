<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Core' => APP_PATH . '/core/',
    'Lib' => APP_PATH . '/lib/',
]);

$loader->register();
