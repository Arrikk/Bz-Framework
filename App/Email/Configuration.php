<?php

namespace App\Email;

use AllowDynamicProperties;
use App\Controllers\Settings\Settings;

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
    private $from_email;
    private $from_name;
    private $password;
    private $username;
    private $smtp_secure;
    private $smpt_host;
    private $smtp_port;
    public $form_creation;
    public $document_upload;
    public $employee_creation;
    public $form_response;

    private function __construct(int $companyID)
    {
        $settings = Settings::settings($companyID, 'email_settings');
        $notification = Settings::settings($companyID, 'email_notification');

        if ($settings)
            foreach ($settings as $key => $value) {
                $this->{$key} = $value;
            }

        if ($notification)
            foreach ($notification as $key => $value) {
                $this->{$key} = $value;
            }


        return $this;
    }

    public static function configure(int $companyID)
    {
        if (self::$class === null) self::$class = new Configuration($companyID);
        return self::$class;
    }

    public static function mail($to, $subject, $body)
    {

        $class = self::$class;
        // $config = Config::config();
        error_reporting(0);
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = (string) $class->smpt_host;
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

        try {
            // $mail->send();
            if (!$mail->send()) {
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
