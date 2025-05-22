<?php

namespace App\Controllers\Authenticated;

use App\Controllers\Subscriptions\SubscriptionService;
use App\Models\Subscription;
use App\Models\User;
use App\Token;
use Core\Controller;
use Core\Http\Res;

class Authenticated extends Controller
{
    protected $user;
    protected $authenticated;
    protected $modelClass;
    protected bool $isAdmin = false;
    protected $subscription;
    protected function before()
    {
        parent::before();
        $header = apache_request_headers();
        if (isset($header['Authorization'])) :

            $token = explode(' ', $header['Authorization']);
            $token = ($token[1] ?? null);
            if ($token = Token::decode($token)) :
                $user = json_decode($token);
                $this->authenticated = $user;
                $this->hasAccess(($user->role ?? null));
                $this->subscription = SubscriptionService::userSubscriptionService($user->id);
                // $this->modelClass =  $user->is_company ?
                //     User::class : (($user->is_employee ?? false) ?
                //         Employee::class : (($user->is_manager ?? false) ?
                //             Manager::class :
                //             Patient::class
                //         )
                //     );

                if (time() > $user->expires) :
                    Res::status(400)->error(['token' => "Token Expired"]);
                endif;
            else :
                Res::status(400)->error(["token" => "Invalid Token"]);
            endif;
        else :
            Res::status(401)->error(["token" => "No Token"]);
        endif;


        if (isset($user->id)) {
            $this->user = User::findOne(['_id' => $user->id, 'or.id' => $user->id]);
            if (!$this->user) Res::status(404)->error([
                'message' => "User not found",
                'token' => $user
            ]);
            $this->isAdmin = $this->user->role === ADMIN;
        }
    }

    function hasAccess($userType)
    {
        $accessTo = ($this->route_params['access'] ?? null);
        if($accessTo == '' || !$accessTo || empty($accessTo)) return;
        if (is_array($accessTo)) :
            if (!in_array($userType, $accessTo)) :
                Res::status(400)->error("Permission denied");
            endif;
        else :
            if ($userType !== $accessTo)
                Res::status(400)::error("Permission denied");
        endif;
    }
}
