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
        $url = rtrim(ltrim($_SERVER['QUERY_STRING']));
        $router->dispatch($url);
    }
}
