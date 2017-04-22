<?php

namespace Lib;

class Log
{
    public static function add($string)
    {
        if (is_array($string)) {
            $string = json_encode($string, JSON_UNESCAPED_UNICODE);
        }
        $fp = fopen(APP_PATH . "/public/log.txt", "a");
        fwrite($fp, "\r\n" . $string);
        fclose($fp);
    }
}