<?php
namespace Core\Http\Requests;

abstract class BaseReq 
{
    abstract function __construct(array $bodyParams, array $headers = []);
    abstract function post($url, $others = '');
    abstract function get($url, $others = '');
    abstract function put($url, $others = '');
    abstract protected function setConfig(array $headers = []);
}