<?php

namespace App\Controllers\Auth;

use App\Helpers\Auth;
use App\Helpers\Logs;
use App\Helpers\Notifications;
use App\Models\User;
use App\Pipes\UserPipe;
use App\Token;
use Core\Controller;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Users extends AuthPipe
{
    /**
     * Register a new user account
     * @param Pipes $body
     */
    public function register(Pipes $body)
    {
        // e.g ['username', $pipe->username]
        $data = $this->registerPipe($body); // Pipe Data (DTO) ...

        $save = (object) User::save($data);
        $apiToken = Token::encodeJSON([
            'id' => "",
            'expires' => strtotime('+2DAYS')
        ]);
        Res::json(
            $save
                ->remove(...userFilters())
                ->append(['token' => $apiToken])
        );
    }

    /**
     * Login user
     * @param Pipes $body
     */
    public function login(Pipes $body)
    {

        $piped = $this->loginPipe($body);

        $auth = Auth::login($piped->email, $piped->password);

        Logs::instance()->login($auth);
        
        Res::json($auth->only('token'), true);
        // Notifications::instance("Hello LoggedIN", "Hrllo There")->firebase("Login");
    }
}
