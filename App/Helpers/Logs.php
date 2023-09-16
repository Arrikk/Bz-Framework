<?php

namespace App\Helpers;

use App\Models\Log;
use App\Models\User;
use Core\Enums\Types;

class Logs
{
    private string $category;
    private static $log;
    private string $ip;
    private $browser;

    private function __construct()
    {
        return $this;
    }

    public static function instance() : Logs
    {
        if (!self::$log instanceof Logs)
            self::$log = new Logs();

            self::$log->ip = IP_ADDRESS;
            self::$log->browser = BROWSER;
            return self::$log;
    }

    public function login(User $user)
    {
        $mssg = "You Logged in on your E-Docs account.";
       return $this->make(Types::LOGIN, $user->_id, $mssg);
    }
    public function createDocument(User $user)
    {
        $mssg = "You created a document on your E-Docs account.";
       return $this->make(Types::UPLOAD_FILE, $user->_id, $mssg);
    }

    private function make($type, $userID, $message)
    {
        return Log::dump([
            '_id' => GenerateKey(30, 50),
            'message' => $message,
            'type' => $type,
            'user_id' => $userID,
            'ip_address' => $this->ip,
            'browser' => json_encode($this->browser)
        ]);
    }
}