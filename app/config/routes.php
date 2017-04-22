<?php

/**
 * Add your routes here
 */
$app->map('/', function () use ($app) {
    $di = $app->getDI();
    $config = $di->getConfig();
    $userId = null;
    $dev = null;
    $secret = null;
    if ($app->request->get('dev')) {
        $dev = true;
        $secret = 'test';
        $type = $app->request->get('type');
        $message = $app->request->get('message');
        $coord = [
            $app->request->get('coord0'),
            $app->request->get('coord1'),
        ];
        $location = [
            'country' => $app->request->get('country'),
            'city' => $app->request->get('city'),
        ];
        if ($app->request->get('user_id')) {
            $userId = $app->request->get('user_id');
        }
        if ($app->request->get('secret')) {
            $secret = $app->request->get('secret');
        }
    } else {
        $dev = false;
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['secret'])) {
            $secret = $data['secret'];
        } else {
            \Lib\Log::add([
                'SECRET NOT' => json_encode($data),
            ]);
            $secret = 'fakeProd';
        }
        $type = isset($data['type']) ? $data['type'] : null;
        $coord = [];
        $location = [];
        if (isset($data['object']) && isset($data['object']['geo'])) {
            if (isset($data['object']['geo']['coordinates'])) {
                $coord = explode(' ', $data['object']['geo']['coordinates']);
            }
            if (isset($data['object']['geo']['place']) && isset($data['object']['geo']['place']['country'])) {
                $location['country'] = $data['object']['geo']['place']['country'];
            }
            if (isset($data['object']['geo']['place']) && isset($data['object']['geo']['place']['city'])) {
                $location['city'] = $data['object']['geo']['place']['city'];
            }
        }
        $message = isset($data['object']) && isset($data['object']['body']) ? $data['object']['body'] : null;
        if (isset($data['object']) && (isset($data['object']['user_id']) || isset($data['object']['from_id']))) {
            if (isset($data['object']['user_id'])) {
                $userId = $data['object']['user_id'];
            }
            if (isset($data['object']['from_id'])) {
                $userId = $data['object']['from_id'];
            }
        }
    }
    foreach ($config->bot as $bot) {
        /** @var \Base\Bot $oBot */
        $oBot = new $bot();
        if ($oBot->check($secret)) {
            $params = [
                'type' => $type,
                'dev' => $dev,
                'user_id' => $userId,
                'message' => $message,
                'geo' => [
                    'coordinates' => $coord,
                    'location' => $location,
                ],
                'attachments' => []
            ];
            if (!$userId) {
                unset($params['user_id']);
            }
            if ($dev) {
                header('Content-Type: application/json');
                \Service\Starter::parseQueue($oBot, $params);
            } else {
                \Core\CommonObject::i()->getDI()->getShared('queue')->put('message', ['bot' => $bot, 'secret' => $secret, 'params' => $params]);
            }
            if ($type == 'confirmation') {
                return $app->response->setContent($oBot->getConfirmToken());
            }
            break;
        }
    }
    return $app->response->setContent('ok');
})->via(['GET', 'POST']);
