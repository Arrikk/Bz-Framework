<?php

namespace App\Controllers;

use App\Controllers\Authenticated\Authenticated;
use App\Mail;
use App\Models\User;
use Core\Http\Res;

class Password extends Authenticated
{
    public function forgot($data)
    {
        // Res::json(Mail::mail('horpeyhermi@gmail.com', 'bruizhacker@gmail.com', 'Test', 'Hello'));
        $this->required(['email' => $data->email ?? '']);
        User::sendPasswordReset($data->email);
    }

    public function token()
    {
        if (isset($this->route_params['token'])) :
            $token = $this->route_params['token'];
            $this->validateToken($token);
        else :
            Res::status(404)->json(['error' => 'Please Provide a token']);
        endif;
    }

    public function reset($data)
    {
        $this->required([
            'password' => $data->password ?? '',
            'token' => $data->token ?? ''
        ]);

        if (isset($data->token) && isset($data->password)) :
            $user = User::findByPasswordReset($data->token);
            $user->resetPassword($data->password);
        endif;
    }

    public function validateToken($token)
    {
        if (User::findByPasswordReset($token))
            Res::json(['message' => 'Token verified']);
    }

    public function _change($data)
    {
        if (isset($data->newPassword) && isset($data->oldPassword)) :
            $user = User::getUserById($this->user->id);
            if ($user->verifyPassword($data->oldPassword)) {
                $user->resetPassword($data->newPassword);
            } else {
                Res::status(401)->json(['error' => 'Old password not correct']);
            }
        endif;
    }
}
