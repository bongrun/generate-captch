<?php

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define("ROOT_DIR", __DIR__ . '/..');
define("APP_PATH", ROOT_DIR);
define("VENDOR_DIR", ROOT_DIR . '/vendor');
define('CONFIG_PATH', BASE_PATH . '/config');
define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'development');

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

if (PHP_MAJOR_VERSION >= 7) {
    set_error_handler(function ($errno, $errstr) {
        return strpos($errstr, 'Declaration of') === 0;
    }, E_WARNING);
}

try {
    // Autoload dependencies
    require VENDOR_DIR . '/autoload.php';

    /**
     * The FactoryDefault Dependency Injector automatically registers the services that
     * provide a full stack framework. These default services can be overidden with custom ones.
     */
    $di = new FactoryDefault();

    /**
     * Include general services
     */
    require CONFIG_PATH . '/services.php';

    /**
     * Include web environment specific services
     */
    require CONFIG_PATH . '/services_web.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include CONFIG_PATH . '/loader.php';

    // наш обработчик ошибок
    function myHandler($level, $message, $file, $line, $context)
    {
        // в зависимости от типа ошибки формируем заголовок сообщения
        switch ($level) {
            case E_WARNING:
                $type = 'Warning';
                break;
            case E_NOTICE:
                $type = 'Notice';
                break;
            default;
                $type = 'Error';
            // это не E_WARNING и не E_NOTICE
            // значит мы прекращаем обработку ошибки
            // далее обработка ложится на сам PHP
//                    return false;
        }
        \Lib\Log::add([
            'ERROR ERROR' => [
                'TYPE' => $type,
                'MESSAGE' => $message,
                'FILE' => $file,
                'LINE' => $line,
                'TRACE' => $context,
            ],
        ]);
        $responseJson['error'] = [
            'type' => $type,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'trace' => $context,
        ];
        echo json_encode($responseJson, JSON_UNESCAPED_UNICODE);
        return true;
    }

    // регистрируем наш обработчик, он будет срабатывать на для всех типов ошибок
    set_error_handler('myHandler', E_ALL);

    /**
     * Handle the request
     */
    $app = new \Phalcon\Mvc\Micro($di);

    \Core\CommonObject::i()->setApp($app);

    while (true) {
        for ($i = \Core\CommonObject::i()->getDI()->getShared('queue')->count('captcha'); $i < 100; $i++) {
            $captcha = new \Lib\Captcha();
            $captcha->generate();
            \Core\CommonObject::i()->getDI()->getShared('queue')->put('captcha', [
                'code' => $captcha->getText(),
                'image' => $captcha->getImage(),
            ]);
        }
        sleep(1);
    }

    return $app;

} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
