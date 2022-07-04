<?php

namespace App\Controllers\Authenticated;

use Core\Controller;
use Core\Http\Res;

/**
 * Authenticated Controller
 */

class Authenticated extends Controller
{
    protected function before()
    {
       parent::before();
        $header = apache_request_headers();
        if (isset($header['Authorization'])) :

            $token = explode(' ', $header['Authorization']);
            $token = $token[1];
            if ($token = $this->jwt('dec', $token)) :
                $this->user = json_decode($token); 
                if(time() > $this->user->expires):
                    Res::status(400)->json(['token' => "Token Expired"]);
                endif;
            else :
                Res::status(400)->json("Invalid Token");
            endif;
        else :
            Res::status(401)->json("No Token");
        endif;
    }

    public function _isUser(int $id = 0)
    {
        $id = isset($this->route_params['id']) ? (int) $this->route_params['id'] : $id; # get User id
        if ((int) $this->user->id !== $id && !$this->user->is_admin) :
            Res::status(401)->json("Illegal Authorization"); # if not admi or another user
        else :
            return $id;
        endif;
    }

    public function _isAdmin($id = 0)
    {
        if (!$this->user->is_admin) :
            Res::status(401)->json("Illegal Authorization"); # if not admin
        else :
            return $this->user->id;
        endif; 
    }
}
