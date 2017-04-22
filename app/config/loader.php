<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Core' => APP_PATH . '/core/',
    'Lib' => APP_PATH . '/lib/',
    'Search' => APP_PATH . '/search/',
    'Command' => APP_PATH . '/command/',
    'Model' => APP_PATH . '/model/',
    'Service' => APP_PATH . '/service/',
    'Base' => APP_PATH . '/base/',
    'Skin' => APP_PATH . '/bot/skin/',
    'Skin\Command' => APP_PATH . '/bot/skin/command/',
    'Fake' => APP_PATH . '/bot/fake/',
    'Fake\Command' => APP_PATH . '/bot/fake/command/',
    'Fake\Service' => APP_PATH . '/bot/fake/service/',
]);

$loader->register();
