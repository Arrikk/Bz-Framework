<?php

namespace App\Controllers\Auth;

use App\Helpers\Auth;
use App\Models\User;
use App\Pipes\UserPipe;
use App\Token;
use Core\Controller;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Users extends Controller
{
    /**
     * Register a new user account
     * @param Pipes $body
     */
    public function register(Pipes $body)
    {
        // e.g ['username', $pipe->username]
        $data = $body->pipe([]); // Pipe Data (DTO) ...

        $save = (object) User::save($data);
        $apiToken = Token::mkToken('enc', json_encode([
            'id' => "",
            'expires' => strtotime('+2DAYS')
        ]));
        Res::json($save->append(['token' => $apiToken]));
    }

    /**
     * Login user
     * @param Pipes $body
     */
    public function login(Pipes $body)
    {
        $piped = $body->pipe([
            'email' => $body->email()->isemail()->email,
            'password' => $body->password()->is_strong_password()->password
        ]);

        $auth = Auth::login($piped->email, $piped->password);

        Res::json($auth->only('token'));
    }
}
