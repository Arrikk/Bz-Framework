<?php

namespace App\Helpers;

use Core\Http\Req;

class Notifications
{
    private static $cls;
    private $message;
    private $deviceToken;
    public static function instance($message, $deviceToken): Notifications
    {
        if (!self::$cls instanceof Notifications)
            $cls = new static();
        $cls->message = $message;
        $cls->deviceToken = $deviceToken;
        return $cls;
    }

    public function firebase($title)
    {
        $serverKey = "";
        $apiUrl = 'https://fcm.googleapis.com/fcm/send';
        $message = [
            'notification' => [
                'title' => $title,
                'body' => $this->message,
            ],
            'data' => [
                'key' => 'value',
            ],
            'to' => $this->deviceToken,
        ];

        Req::sleek($message)->headers([
            'Authorization: Bearer ' . $serverKey,
            'Content-Type: application/json',
        ])->post($apiUrl);
    }
}
