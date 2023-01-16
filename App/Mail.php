<?php

namespace App;

use App\Config;

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

use App\Models\Settings;
use Core\Http\Res;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mail
 * ==============CodeHart(Bruiz)==========
 * PHP V 7.4.8
 */

class Mail
{
    public static function mail($to, $from, $subject, $body)
    {

        // $config = Config::config();
        error_reporting(0);
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = (string) SMTP_HOST;
        $mail->Port = (int) SMTP_PORT;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = (string) SMTP_SECURE;
        $mail->Username   = (string) SMTP_USERNAME;   //SMTP username
        $mail->Password   = (string) SMTP_PASSWORD;
        
        
        $mail->setFrom($from, (string) MAIL_FROM);
        $mail->addAddress($to);
        $mail->addReplyTo($from);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        try {
            // $mail->send();
            if(!$mail->send()) {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            Res::error($e->getMessage());
        }
    }
    /**
     * Send an email
     */
    public static function send($to, $subject, $body, $from)
    {
        $mail = mail($to, $subject, $body, $from);
        if ($mail) {
            return true;
        }
    }
}
