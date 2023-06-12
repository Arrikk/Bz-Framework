<?php

namespace Core;

use Dotenv\Dotenv;

class Env
{
    public static function __callStatic($name, $arguments)
    {
        return self::_getENV($name);
    }

    public static function load()
    {
        // echo dirname(__DIR__)."Hone";
        $path = './';
        if (file_exists($path . '.env')) :
            $dotenv = Dotenv::createImmutable('./');
            $dotenv->load();
        endif;
    }

    public static function _getENV($name)
    {
        $env = getenv($name);
        if (isset($env) && $env !== false)
            return $env;
        else
            return isset($_ENV[$name]) ? $_ENV[$name] : null;
    }
}
