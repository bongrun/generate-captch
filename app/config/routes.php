<?php

/**
 * Add your routes here
 */
$app->map('/', function () use ($app) {
    if (!\Core\CommonObject::i()->getDI()->get('cookies')->has("identify")) {
        \Core\CommonObject::i()->getDI()->get('cookies')->set("identify", md5(microtime() . $_SERVER['REMOTE_ADDR']));
    }
    $simpleView = new \Phalcon\Mvc\View();
    $simpleView->setViewsDir(APP_PATH . '/views/');
    $simpleView->setLayoutsDir(APP_PATH . '/layout/');
    $simpleView->setLayout('index');
    return $simpleView->render("post", "index", []);
})->via(['GET']);

$app->map('/', function () use ($app) {
    $result = 'Капча введена не верно';
    if ($app->request->get('captcha') == \Core\CommonObject::i()->getDI()->getShared('cache')->get('captcha' . \Core\CommonObject::i()->getDI()->get('cookies')->get("identify"))) {
        $result = 'Капча введена верно';
    }
    $simpleView = new \Phalcon\Mvc\View();
    $simpleView->setViewsDir(APP_PATH . '/views/');
    $simpleView->setLayoutsDir(APP_PATH . '/layout/');
    $simpleView->setLayout('index');
    return $simpleView->render("post", "result", ['result' => $result]);
})->via(['POST']);

$app->get('/captcha', function () use ($app) {
    $result = \Core\CommonObject::i()->getDI()->getShared('queue')->pullOne('captcha');
    if (!$result) {
        throw new Error('Нет капч');
    }
    \Core\CommonObject::i()->getDI()->getShared('cache')->save('captcha' . \Core\CommonObject::i()->getDI()->get('cookies')->get("identify"), $result['code'], 60 * 15);
    $im = imagecreatefrompng($result['image']);
    header('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);
    unlink($result['image']);
});
