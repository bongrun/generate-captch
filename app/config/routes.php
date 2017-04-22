<?php

/**
 * Add your routes here
 */
$app->map('/', function () use ($app) {
    $result = \Core\CommonObject::i()->getDI()->getShared('queue')->pullOne('captcha');
    if (!$result) {
        throw new Error('Нет капч');
    }
    $salt = md5(microtime() . $_SERVER['REMOTE_ADDR']);
    \Core\CommonObject::i()->getDI()->getShared('cache')->save('captcha'.$salt, $result['code'], 60 * 5);
    $result['salt'] = $salt;
    $simpleView = new \Phalcon\Mvc\View\Simple();
    $simpleView->setViewsDir(APP_PATH . '/views/');
    return $app->response->setContent($simpleView->render("index", $result));
})->via(['GET', 'POST']);
