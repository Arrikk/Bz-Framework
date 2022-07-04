<?php

namespace Core\Http;
use Core\Interfaces\Resinterface;
/**
 * Base Response
 */

final class Res implements Resinterface
{
    public static function status(int $status = 200)
    {
        http_response_code($status);
        return new Res;
    }

    public static function json($message = null)
    {
        header('content-type: application/json');
        echo json_encode($message);
        return exit;
    }

    public static function send($message = null)
    {
        echo $message;
        return exit;
    }

    public static function raw($message = null)
    {
        return $message;
        exit;
    }
        
    public static function ip()
    {
        return getenv('HTTP_CLIENT_IP') ?:
            getenv('HTTP_X_FORWARDED_FOR') ?:
            getenv('HTTP_X_FORWARDED') ?:
            getenv('HTTP_FORWARDED_FOR') ?:
            getenv('HTTP_FORWARDED') ?:
            getenv('REMOTE_ADDR');
    }
}
