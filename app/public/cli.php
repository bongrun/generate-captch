<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$app = require __DIR__ . '/../bootstrap/bootstrap_cli.php';

/**
 * Handle Request
 */
$app->handle();
