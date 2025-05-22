<?php

namespace App\Email;

use App\Email\Configuration;
use Core\Http\Res;
use Twig\Template;

class Emails extends Configuration
{
    public static function sample(string $email, $name = "")
    {
        if (self::$class->form_creation)
            self::sendEmail(
                $email,
                $name,
                "Form Creation",
                "You just created a form , YOu might like to share and accept response.",
                "New "
            );
    }

    private static function sendEmail($email, $name, $type, $message, $subject)
    {
        try {
            //code...
            return  Emails::mail($email, "Re: ", Templates::EmailNotifications($name, "Form Response", "You have a response to a form you created, Login to your account to view more details..."));
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)->error($th->getMessage());
        }
    }
}
