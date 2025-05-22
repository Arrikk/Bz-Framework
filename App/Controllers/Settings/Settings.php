<?php
namespace App\Controllers\Settings;

use App\Controllers\Authenticated\Authenticated;
use App\Models\Setting;
use Core\Http\Res;
use Module\Image;

class Settings extends SettingService
{
    public function _set($data)
    {
        $data = $this->settingPipe($data);
        // Res::json($data);
        $data->user_id = $this->user->id;
        if(!is_string($data->logo)) $data->logo = (new Image($data->logo))->upload();
        $saved = $this->settingService($data);
        Res::json($saved);
    }

    public function _get()
    {
        Res::send($this->settings());
    }

    public static function settings($key = null)
    {
        try {
            //code...
            $setting = Setting::findOne(['user_id' => "1"]);
            if(!$key) return self::formatData($setting);
         //    Res::send($setting);
            if($setting !== null && $key !== null && $setting) return self::formatData($setting)->{$key};
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)::error($th->getMessage());
        }
    }
}