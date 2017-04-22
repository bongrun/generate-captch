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
    "mongo",
    function () {

//        if (\Search\Query::i()->getOption('secret') == 'prod') {
        $mongo = new \Phalcon\Db\Adapter\MongoDB\Client('mongodb://mongo:27017');
//        } else {
//            $mongo = new \Phalcon\Db\Adapter\MongoDB\Client('mongodb://mongo-test:27017');
//        }
        return $mongo->selectDatabase('vkbot');


        $cc = new MongoDB\Client('mongodb://mongo:27017');
        return $cc->selectDatabase('vkbot');
        return $cc->selectCollection('test', 'users');
//        $cc->sele
        $client = new MongoDB($cc, 'demo');

        $collection = $client->selectCollection('demo');

        return $collection;

        $mongo = new MongoDB\Driver\Manager('mongodb://mongo:27017');
//        var_dump($mongo->getServers());
//
//        $command = new MongoDB\Driver\Command(['ping' => 1]);
//        $mongo->executeCommand('db', $command);
//
//        var_dump($mongo->getServers());
//        die;
//
//        return $mongo->getServers();
        $rr = new \MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_SECONDARY_PREFERRED);
        $tt = $mongo->selectServer($rr);
//        $tt->
        return $mongo->selectServer("store");
    },
    true
);

$di->set(
    "collectionManager",
    function () {
        $eventsManager = new EventsManager();

        // Attach an anonymous function as a listener for "model" events
        $eventsManager->attach(
            "collection:beforeSave",
            function (Event $event, $model) {
//                if (get_class($model) === "Model\\User") {
//                    if ($model->name === "Scooby Doo") {
//                        echo "Scooby Doo isn't a robot!";
//
//                        return false;
//                    }
//                }

                return true;
            }
        );

        // Setting a default EventsManager
        $modelsManager = new CollectionManager();

        $modelsManager->setEventsManager($eventsManager);

        return $modelsManager;
    },
    true
);

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