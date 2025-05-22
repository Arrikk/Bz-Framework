<?php

namespace App\Email;

use AllowDynamicProperties;
use App\Controllers\Settings\Settings;
use Core\Env;
use Core\Http\Res;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mail
 * ==============CodeHart(Bruiz)==========
 * PHP V 7.4.8
 */

#[AllowDynamicProperties]
class Configuration
{
    public static $class;
    private bool $silentlySend = false;
    private $from_email;
    private $from_name;
    private $password;
    private $username;
    private $smtp_secure;
    private $smtp_host;
    private $smtp_port;

    public function __construct(int $companyID = 1)
    {
        // $settings = Settings::settings($companyID, 'email_settings');
        // $notification = Settings::settings($companyID, 'email_notification');

        // if ($settings)
        //     foreach ($settings as $key => $value) {
        //         $this->{$key} = $value;
        //     }
        $this->smtp_port = Env::SMTP_PORT();
        $this->smtp_host = Env::SMTP_HOST();
        $this->smtp_secure = Env::SMTP_SECURE();
        $this->username = Env::SMTP_USERNAME();
        $this->password = Env::SMTP_PASSWORD();
        $this->from_email = Env::MAIL_FROM();
        $this->from_name = Env::MAIL_FROM_NAME();

        // if ($notification)
        //     foreach ($notification as $key => $value) {
        //         $this->{$key} = $value;
        //     }


        return $this;
    }

    public static function configure(int $companyID = 1)
    {
        if (self::$class === null) self::$class = new Configuration($companyID);
        return self::$class;
    }

    public static function silentlySend(){
        self::$class->silentlySend = true;
        return self::$class;
    }

    public static function mail($to, $subject, $body, $attachment = null)
    {

        $class = self::$class;

            // Res::send([
            //     $class->smtp_host,
            //     $class->username,
            //     $class->smtp_secure
            // ]);
        // $config = Config::config();
        error_reporting(0);
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = (string) $class->smtp_host;
        $mail->Port = (int) $class->smtp_port;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = (string) $class->smtp_secure;
        $mail->Username   = (string) $class->username;   //SMTP username
        $mail->Password   = (string) $class->password;

        $mail->setFrom($class->from_email, (string) $class->from_name);
        $mail->addAddress($to);
        $mail->addReplyTo($class->from_email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        if($attachment) $mail->addAttachment($attachment, "Copy of Document");

        try {
            // $mail->send();
            if (!$mail->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            if(!$class->silentlySend)
                Res::status(400)->throwable($e);
            return true;
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
