<?php

namespace App\Controllers\Auth;

use App\Helpers\Auth;
use App\Helpers\Logs;
use App\Helpers\Notifications;
use App\Models\Referral;
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
        $data = $this->registerPipe($body); // Pipe Data (DTO) ..

        $save = (object) User::save($data);
        if($body->referral_id) {
            $id = preg_replace('/\w+/', $body->referral_id, "");
            $referral = User::findOne(['id' => $id]);
            if($referral) {
                Referral::dump([
                    'referred_id' => $save->id,
                    'status' => PENDING,
                    'source' => $body->ref_source ?? 'direct',
                    'referrer_id' => $referral->id,
                    'plan' => 'free'
                ]);
            }
        }
        $apiToken = Token::encodeJSON([
            'id' => $save->id,
            '_id' => $save->id,
            'role' => $save->role,
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
