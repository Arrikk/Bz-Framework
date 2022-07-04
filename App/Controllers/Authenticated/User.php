<?php

namespace App\Controllers;

use App\Controllers\Authenticated\Authenticated;
use App\Models\Feed\Tips;
use App\Models\Settings\Settings;
use App\Models\User as ModelsUser;
use App\Models\User\Friends;
use App\Models\User\Suggestions;
use App\Models\Wallet\Wallet;
use Core\Http\Res;
use Core\View;

class User extends Authenticated
{
    public function index()
    {
        // Res::send("<h1>Leviplatte API</h1>");
        View::render('dashboard/index.html');
    }
    public function create($user)
    {
        // Res::json($user);
        $user->username = "Lev" . rand(9999, 999999999);
        $userModel = new ModelsUser($user);
        if ($user = $userModel->save()) # save credentials
            return Res::status(200)->json($user); #Return if true
        return Res::send($user); # return if error
    }


    public function login($data)
    {
        if (isset($data->email) && isset($data->password)) :
            if ($user = ModelsUser::authenticate($data->email, $data->password)) :
                $token = $this->jwt('enc', json_encode([
                    'id' => (int) $user->id,
                    'is_admin' => (int) $user->is_admin == 1 ? true : false,
                    'expires' => time() + 60 * 60 * 24
                ]));
                Res::json(['token' => $token]);
            else :
                Res::send($user);
            endif;
        else :
            Res::status(400)->send('Fields error');
        endif;
    }

    // public function _get()
    // {
    //     $id = $this->user->id;
    //     $user = ModelsUser::getUser($id);
    //     $settings = Settings::setting($id);
    //     $wallet = Wallet::getWallet($id);

    //     $output = array_merge((array) $user, ['wallet' => $wallet->balance]);

    //     foreach($settings as $val):
    //         $output['display'] = $val->setting_name == 'mode' ? $val->setting_value : 'light';
    //         if($val->setting_name == 'mode') continue;
    //         $output[$val->setting_name] = $val->setting_value ? true : false;
    //     endforeach;

    //     Res::json($output);

    // }
    // public function user()
    // {
    //     $id = (int) $this->route_params['user'] ?? '';
    //     $user = ModelsUser::getUser($id);

    //     Res::json($user);

    // }

    public function _users($data)
    {
        $this->isAdmin();
        $users = ModelsUser::users($this->user->id, $data);
        Res::json($users);
    }

    public function getUserListAnalytic()
    {
        Res::json(ModelsUser::userListCount());
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
            Res::json(ModelsUser::updateUser($id, $update)); # Update if is user and is admin
        else :
            Res::json(['message' => "Development in pregress"]);
        # update with token
        endif;
    }

    public function _delete()
    {
        $id = $this->isUser();
        Res::json(ModelsUser::deleteUser($id));
    }

    public function exists($user)
    {
        if (!isset($user->check))
            Res::status(400)->json(['error' => 'Provide email or username to check']);
        Res::json(['exists' => ModelsUser::userExists($user->check)]);
    }

    public function _follow($data)
    {
        $to = $this->route_params['id'] ?? false;
        if ( $to ) :
        $data->toFollow = $to;
        $data->isFollowing = $this->user->id;
        if($follow = Friends::friend($data))
            Res::json(['status' => true, 'message' => $follow]);
            Res::status(400)->json(['error' => 'Operation Failed']);
        else:
            Res::status(400)->json(['error' => "Action Denied"]);
        endif;
    }

    public function getFullProfile()
    {
        $id = $this->route_params['id'];
        Res::json(ModelsUser::getFullProfile($id));
    }

    public function changeEmail()
    {
        
    }

    public function _tip($data)
    {
        $param = [
            'amount' => $data->amount ?? '',
            'beneficiary' => $data->beneficiary ?? '',
            'currentUser' => $this->user->id
        ];

        $this->required($param);
        Res::json(Tips::tipUser((object) $param));
    }

    public function _suggestions()
    {
        Res::json(Suggestions::suggest($this->user->id));
    }
}
