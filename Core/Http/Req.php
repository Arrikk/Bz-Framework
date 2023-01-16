<?php

namespace Core\Http;

use Core\Http\Requests\Bruiz as RequestsBruiz;
use Core\Calls\Override;
use Core\Http\Requests\Guzzle as RequestsGuzzle;
use Core\Interfaces\Req as InterfacesReq;

class Guzzle extends RequestsGuzzle
{
}

class Bruiz extends RequestsBruiz
{
}

class Req extends Override implements InterfacesReq
{
    public static function slim($body = [], $headers = []) : Bruiz
    {
        return new Bruiz($body, $headers);  
    }

    public static function sleek($body = [], $headers = []): Guzzle
    {
        return new Guzzle();
        // return Guzzle::class;
    }
}
