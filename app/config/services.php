<?php

use Phalcon\Loader;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;


/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include CONFIG_PATH . "/config.php";
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
//$di->setShared('modelsMetadata', function () {
//    return new MetaDataAdapter();
//});