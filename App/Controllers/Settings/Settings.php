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
        Res::send($this->settings($this->companyID, null, $this->user->id));
    }

    public static function settings($userID, $key = null, $user = null)
    {
        try {
            //code...
            $setting = Setting::findOne(['user_id' => $userID]);
            if(!$key) return self::formatData($setting, $user);
         //    Res::send($setting);
            if($setting !== null && $key !== null && $setting) return self::formatData($setting, $user)->{$key};
        } catch (\Throwable $th) {
            //throw $th;
            Res::status(400)::error($th->getMessage());
        }
    }
}