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
            'about' => $pipe->about()->serialize()->about,
            'email_notifications' => $pipe
                ->email_notifications()
                ->object([
                    'employee_login' => 'isbool',
                    'employee_creation' => 'isbool',
                    'account_login' => 'isbool',
                    'form_creation' => 'isbool',
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
        ]);
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
        if ($settings->user_id == $user || $user == null)
            return $settings->append([
                'options' => json_decode($settings->options),
                'email_notification' => json_decode($settings->email_notification),
                'email_settings' => json_decode($settings->email_settings),
                'app_access' => json_decode($settings->app_access)
            ]);

        return $settings->append([
            'options' => json_decode($settings->options),
            'app_access' => json_decode($settings->app_access)
        ])->only('options','app_access','id');
    }
}
