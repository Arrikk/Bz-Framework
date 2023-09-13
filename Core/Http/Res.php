<?php

namespace Core\Http;

use Core\Interfaces\Resinterface;
use Throwable;

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

    public static function json($message = null, $soft = false)
    {
        header('content-type: application/json');
        echo json_encode($message);
        if(!$soft) return exit;
    }
    public static function error($message = null, $soft = false)
    {
        header('content-type: application/json');
        echo json_encode(['error' => $message]);
        if(!$soft) return exit;
    }

    public static function send($message = null, $soft =  false)
    {
        header('content-type: application/json');
        if (is_array($message) || json_encode($message) !== '{}') :
            echo json_encode($message);
        else :
            echo ($message);
        endif;

        if(!$soft) return exit;
    }

    public static function raw($message = null)
    {
        return $message;
        exit;
    }

    public static function throwable(Throwable $th, $withTrace = false, $soft = false)
    {
        header('content-type: application/json');
        echo json_encode([
            "message" => $th->getMessage(),
            "file" => $th->getFile(),
            "line" => $th->getLine(),
            "code" => $th->getCode(),
            ($withTrace) ?? "trace" => $th->getTraceAsString()
        ]);
        if(!$soft) return exit;
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
