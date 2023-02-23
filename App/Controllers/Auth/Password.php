<?php

namespace App\Controllers\Auth;

use App\Controllers\Authenticated\Authenticated;
use App\Models\User;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Password extends Authenticated
{
    /**
     * Change User Password
     * Method requires _ to require authentication
     * @param Pipes $req... Body Params
     */
    public function _change(Pipes $req)
    {
        $body = $req->pipe([
            'old_password' => $req->old_password()->isrequired()->old_password,
            'new_password' => $req->new_password()->isrequired()->is_strong_password()->new_password,
            'confirm_password' => $req->confirm_password()->isequal($req->new_password)->is_strong_password()->confirm_password,
        ]);

        $user = $this->user;

        if (!$user->verifyPassword($body->old_password))
            Res::status(400)::error("Incorrect old Password");

        $user->resetPassword($body->new_password);
    }

    /**
     * Forgot password... 
     * Send email reset password link..
     * @param Pipes $req... Body request
     */
    public function forgot(Pipes $req)
    {
        $this->required(['email' => $req->email ?? '']);
        User::sendPasswordReset($req->email);
    }

    /**
     * Validate Token from email
     */
    public function token()
    {
        if (isset($this->route_params['token'])) :
            $token = $this->route_params['token'];
            if(User::findByPasswordReset($token)) Res::json(["message" => "Token Verified"]);
            Res::status(400)->error("Invalid Token");
        else :
            Res::status(404)->error('Please Provide a token');
        endif;
    }


    /**
     * Reset User Password...
     * @param Pipes $req... Bodyrequest
     */
    public function reset(Pipes $req)
    {
        $data = $req->pipe([
            'token' => $req->token()->isrequired()->token,
            'password' => $req->password()->min(8)->is_strong_password()->password
        ]);

        $user = User::findByPasswordReset($data->token);
        $user->resetPassword($data->password);
    }
}
