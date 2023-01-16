<?php
namespace Core\Interfaces;

use Core\Http\Requests\BaseReq;

interface Req
{
 public static function slim($body = [], $headers = []) : BaseReq;
 public static function sleek($body = [], $headers = []) : BaseReq;
}