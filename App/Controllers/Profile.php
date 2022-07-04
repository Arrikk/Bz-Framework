<?php
namespace App\Controllers;

use App\Controllers\Authenticated\Authenticated;
use App\Models\Settings\Settings;
use App\Models\User;
use App\Models\User\Followers;
use Core\Http\Res;
use Module\Image;

class Profile extends Authenticated
{
    public function _profile()
    {
        $profile = $this->route_params['user'];
        $profile = User::getUserById($profile);

        $user = User::getUser($profile->id, $this->user->id);
        $friends = Followers::followings($profile->id);
        $user->friends = $friends;
        Res::json($user);
    }

    public function _myProfile()
    {
        $profile = $this->user->id;
        $profile = User::getUserById($profile);

        $user = User::getUser($profile->id);
        $friends = Followers::followings($profile->id);
        $user->friends = $friends;
        
        $settings = Settings::setting($profile->id);
        $output = [];

        foreach($settings as $val):
            $output['display'] = $val->setting_name == 'mode' ? $val->setting_value : 'light';
            if($val->setting_name == 'mode') continue;
            $output[$val->setting_name] = $val->setting_value ? true : false;
        endforeach;

        $user = array_merge((array) $user, $output);

        Res::json($user);
    }

    public function _update($update)
    {
        // Res::json($update);

        $id = $this->_isUser();
        if (!isset($update->withToken)) :
            if (
                isset($update->is_admin)
                || isset($update->is_verified)
                || isset($update->is_active)
                || isset($update->email)
            ) :
                Res::status(400)->json(['error' => 'Some fields needs an additional Informations']);
            endif;
            Res::json(User::updateUser($id, $update, $_FILES)); # Update if is user and is admin
        else :
            Res::json(['message' => "Development in pregress"]);
        # update with token
        endif;
    }

    public function upload($data)
    {
        
        // Res::json($_FILES['image']);
        // Res::json(is_array($_FILES['image']));

        Res::json(Image::multiple($_FILES));
    }
}