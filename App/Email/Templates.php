<?php
namespace App\Email;

use Core\Http\Res;

class Templates
{
    public static function EmailActivation($data = [])
    {
        extract($data);
        $url = dirname(__DIR__).'/Views/emailTemplates/emails_activate.html';
        $file = file_get_contents($url);
        $file = str_replace('{{ token }}', $token, $file);
        $file = str_replace('{{ email }}', $email, $file);
        return $file;
    }

    public static function forgotEmail($data = []) 
    {
        extract($data);
        $url = dirname(__DIR__).'/Views/emailTemplates/emails_forgot.html';
        $file = file_get_contents($url);
        // $file = str_replace('{{ token }}', $token, $file);
        $file = str_replace('{{ email }}', $email, $file);
        $file = str_replace('{{ URL }}', $URL, $file);
        return $file;
    }

    public static function EmailNotifications ($name, $type, $message)
    {
        try {
            $url = dirname(__DIR__).'/Views/emailTemplates/notification_template.html';
            $file = file_get_contents($url);
            // $file = str_replace('{{ token }}', $token, $file);
            $file = str_replace('{{ name }}', $name, $file);
            $file = str_replace('{{ type }}', $type, $file);
            $file = str_replace('{{ message }}', $message, $file);
            return $file;
        } catch (\Throwable $th) {
            Res::status(404)->error("Cannot Load Notification Template File,,, Filenot found.");
        }
    }
}