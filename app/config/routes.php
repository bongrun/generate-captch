<?php

/**
 * Add your routes here
 */
$app->map('/', function () use ($app) {
//    $di = $app->getDI();
//    $config = $di->getConfig();
//
//    \Lib\Log::add([
//        'SECRET NOT' => json_encode($data),
//    ]);
//
//    header('Content-Type: application/json');
//    \Service\Starter::parseQueue($oBot, $params);
//    \Core\CommonObject::i()->getDI()->getShared('queue')->put('message', ['bot' => $bot, 'secret' => $secret, 'params' => $params]);
    return $app->response->setContent('ok');
})->via(['GET', 'POST']);
