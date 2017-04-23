<?php

use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Collection\Manager as CollectionManager;
use Phalcon\Cache\Backend\Redis;
use Phalcon\Cache\Frontend\Data as FrontData;

/**
 * Registering a router
 */
$di->setShared('router', function () {
    $router = new Router();

//    $router->setDefaultModule('frontend');

    return $router;
});

/**
 * Set the default namespace for dispatcher
 */
$di->setShared('dispatcher', function () {
    $dispatcher = new Dispatcher();
//    $dispatcher->setDefaultNamespace('Api\Modules\Frontend\Controllers');
    return $dispatcher;
});

$di->set(
    "cache",
    function () {
        $frontCache = new FrontData(
            [
                "lifetime" => 172800,
            ]
        );

        // Create the Cache setting redis connection options
        $cache = new Redis(
            $frontCache,
            [
                "host" => "redis",
                "port" => 6379,
                "auth" => "redis",
                "persistent" => true,
                "index" => 0,
            ]
        );

        return $cache;
    },
    true
);

$di->setShared('queue', function () {
    $queue = new \Lib\Queue(new \PhpAmqpLib\Connection\AMQPConnection(
        'rabbitmq',    #host - имя хоста, на котором запущен сервер RabbitMQ
        5672,        #port - номер порта сервиса, по умолчанию - 5672
        'guest',        #user - имя пользователя для соединения с сервером
        'guest'        #password
        , '/'
        , 60
    ));
    return $queue;
});

$di->set(
    "crypt",
    function () {
        $crypt = new \Phalcon\Crypt();

        $crypt->setKey('#1dj8$=dp?.ak//j1V$-=so*(90ak@-');

        return $crypt;
    }
);

$di->set(
    "cookies",
    function () {
        $cookies = new \Phalcon\Http\Response\Cookies();
        return $cookies;
    }
);