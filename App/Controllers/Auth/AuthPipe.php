<?php
namespace App\Controllers\Auth;

use Core\Controller;
use Core\Pipes\Pipes;

class AuthPipe extends Controller
{
    public function registerPipe(Pipes $pipe)
    {
        return $pipe->pipe([
            'email' => $pipe->email()->isemail()->email,
            'username' => $pipe->username()->min(4)->max(8)->match('/^[a-z0-9]+$/')->username,
            'password_hash' => $pipe->password()->is_strong_password()->password,
        ]);
    }
}
