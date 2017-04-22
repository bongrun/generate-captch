<?php

namespace Core;

use Base\Bot;
use Model\User;
use Phalcon\Cache\Backend\Redis;

class CommonObject
{
    /**
     * @var \Phalcon\Mvc\Micro
     */
    private $app;
    /** @var  User */
    private $user;

    private $values = [];
    /** @var Bot */
    private $bot;

    private static $implement = null;

    /**
     * @return CommonObject
     */
    public static function i()
    {
        if (is_null(self::$implement)) {
            self::$implement = new CommonObject();
        }
        return self::$implement;
    }

    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * @return \Phalcon\DiInterface
     */
    public function getDI()
    {
        return $this->app->getDI();
    }

    /**
     * @return Redis
     */
    public function getCache()
    {
        return $this->getDI()->get('cache');
    }

    /**
     * @return \Phalcon\Config
     */
    public function getConfig()
    {
        return $this->getDI()->getConfig();
    }

    /**
     * @param $key
     * @param $value
     */
    public function addValue($key, $value)
    {
        if (!isset($this->values[$key])) {
            $this->values[$key] = [];
        }
        $this->values[$key][] = $value;
    }

    public function getValue($key)
    {
        if (!isset($this->values[$key])) {
            return null;
        }
        return $this->values[$key];
    }
}