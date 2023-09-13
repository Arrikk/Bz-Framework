<?php

namespace App\Controllers\Auth;

use Core\Controller;
use Core\Pipes\Pipes;

class AuthPipe extends Controller
{
    public function registerPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'email' => $pipe
                ->email()
                ->isemail()->email,
            // 'fullname' => $pipe
            //     ->fullname()
            //     ->min(5)
            //     ->match('/^[\da-z ]+$/i')
            //     ->fullname,
            'first_name' => $pipe->firstname()->min(2)->match('/^[\da-z ]+$/i')->firstname,
            'last_name' => $pipe->lastname()->min(2)->match('/^[\da-z ]+$/i')->lastname,
            'password_hash' => $pipe
                ->password()
                ->isrequired()
                ->is_strong_password()
                ->password,
                "_id" => GenerateKey(30, 50)
        ]);
    }

    public function loginPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'email' => $pipe
                ->email()
                ->isemail()
                ->email,
            'password' => $pipe
                ->password()
                ->isrequired()
                ->password
        ]);
    }
}
