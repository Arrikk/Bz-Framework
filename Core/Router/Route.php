<?php

namespace Core\Router;

use Core\Request;

class Route
{

    public static function ENV()
    {
        // echo dirname(__DIR__)."Hone";
        // $dotenv = Dotenv::createImmutable('./');
        // $dotenv->load();
    }

    public static function Route()
    {
        Request::cors();
        $router = new Router;
        require 'Router/Routes.php';
        require 'Utils/utils.php';
        require 'App/Variables.php';

        /**
         * Match the Requested Url
         */
        $path = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : $_SERVER['REQUEST_URI']);

        $position = strpos($path, '/');

        // Remove the substring if found
        if ($position !== false) {
            $path = substr_replace($path, "", $position, strlen('/'));
        }
        $url = rtrim(ltrim($path));
        $router->dispatch($url);
    }
}
