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
            'fullname' => $pipe
                ->fullname()
                ->min(5)
                ->match('/^[\da-z ]+$/i')
                ->fullname,
            'password_hash' => $pipe
                ->password()
                ->isrequired()
                ->is_strong_password()
                ->password
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
