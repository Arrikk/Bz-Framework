<?php

namespace App\Controllers\Settings;

use App\Controllers\Authenticated\Authenticated;
use App\Models\Setting;
use Core\Http\Res;
use Core\Pipes\Pipes;

class SettingService extends Authenticated
{
    public function settingPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'logo' => $pipe->logo,
            'about' => $pipe->about()->sanitize()->about,
            'email_notifications' => $pipe
                ->email_notifications()
                ->object([
                    'email_notification' => 'isbool',
                    'subscription_changes' => 'isbool',
                    'new_user' => 'isbool',
                    'support_ticket' => 'isbool',
                ])->email_notifications,
            'email_settings' => $pipe
                ->email_settings()
                ->object([
                    "smpt_host" => "isstring",
                    "smtp_port" => "isstring",
                    "smtp_secure" => "isstring",
                    "username" => "isstring",
                    "password" => "isstring",
                    "from_email" => "isstring",
                    "from_name" => "isstring",
                ])->email_settings,
            'app_access' => $pipe
                ->app_access()
                ->object([
                    "otp_login" => "isbool",
                    "login_otp_expiry" => "isstring",
                    "user_session_expiry" => "isstring",
                    "email_notifications" => "isbool",
                    "app_notifications" => "isbool"
                ])->app_access,
            'stripe_api' => $pipe
                ->stripe_api()
                ->object([
                    "public_key" => "isstring",
                    "secret_key" => "isstring",
                    "webhook_url" => "isstring"
                ])->stripe_api,
        ]);
    }
    public function emailPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'email_settings' => $pipe
                ->email_settings()
                ->object([
                    "smpt_host" => "isstring",
                    "smtp_port" => "isstring",
                    "smtp_secure" => "isstring",
                    "username" => "isstring",
                    "password" => "isstring",
                    "from_email" => "isstring",
                    "from_name" => "isstring",
                ])->email_settings
        ]);
    }
    public function stripePipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'stripe_api' => $pipe
                ->stripe_api()
                ->object([
                    "public_key" => "isstring",
                    "secret_key" => "isstring",
                    "webhook_url" => "isstring"
                ])->stripe_api,
        ]);
    }
    public function notificationPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'notification_settings' => $pipe
                ->notification_settings()
                ->object([
                    'email_notification' => 'isbool',
                    'subscription_changes' => 'isbool',
                    'new_user' => 'isbool',
                    'support_ticket' => 'isbool',
                ])->notification_settings,
        ]);
    }
    public function generalPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'general_settings' => $pipe
                ->general_settings()
                ->object([
                    "company_name" => "string",
                    "support_mail" => "isstring",
                    "time_zone" => "isstring",
                    "theme" => "isstring",
                ])->general_settings,
        ]);
    }

    public function settingServiceNew($data)
    {
        $save = [
            'user_id' => $data->user_id,
        ];
        if(isset($data->general_settings)) $save['app_access'] =  json_encode($data->general_settings);
        if(isset($data->stripe_api)) $save['stripe_settings'] =  json_encode($data->stripe_api);
        if(isset($data->email_settings)) $save['email_settings'] =  json_encode($data->email_settings);
        if(isset($data->notification_settings)) $save['notification_settings'] =  json_encode($data->notification_settings);
        if ($settings = Setting::findOne(['user_id' => $data->user_id]))
            $svd = $settings->modify((array) $save);
        else
            $svd = Setting::dump((array) $save);
        return $this->formatData($svd);
    }
    public function settingService($data)
    {
        $save = [
            'user_id' => $data->user_id,
            'options' => json_encode([
                'logo' => $data->logo,
                'about' => $data->about
            ]),
            'email_notification' => json_encode($data->email_notifications),
            'email_settings' => json_encode($data->email_settings),
            'app_access' => json_encode($data->app_access),
            'stripe_api' => json_encode($data->stripe_api),
        ];
        if (Setting::findOne(['user_id' => $data->user_id]))
            $svd = Setting::findAndUpdate(['user_id' => $data->user_id], (array) $save);
        else
            $svd = Setting::dump((array) $save);
        return $this->formatData($svd);
    }

    public static function formatData($settings, $user = null)
    {
        if (!$settings) return;
        // if ($settings->user_id == $user || $user == null)
            return $settings->append([
                'options' => $settings->options !== null ? json_decode($settings->options) : null,
                'notification_settings' => $settings->notification_settings !== null ? json_decode($settings->notification_settings) : null,
                'email_settings' => $settings->email_settings !== null ? json_decode($settings->email_settings) : null,
                'general_settings' => $settings->app_access !== null ? json_decode($settings->app_access) : null,
                'stripe_api' => $settings->stripe_settings !== null ? json_decode($settings->stripe_settings) : null
            ])->remove('stripe_settings', 'app_access');

        // return $settings->append([
        //     'options' => json_decode($settings->options),
        //     'app_access' => json_decode($settings->app_access)
        // ]);
    }
}
