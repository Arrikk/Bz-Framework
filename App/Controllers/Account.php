<?php

namespace App\Controllers;

use App\Controllers\Authenticated\Authenticated;
use Core\Http\Res;
use Core\Pipes\Pipes;
use Module\File\File;

class Account extends Authenticated
{
    /**
     * Get Current User Profile
     */
    public function _profile()
    {
        Res::json(
            $this->user
                ->remove(...userFilters())
        );
    }

    /**
     * Update User Profile
     * @param Pipes $req... body params
     */
    public function _update(Pipes $req)
    {
        $req = $req->pipe([
            'first_name' => $req
                ->firstname()
                ->default($this->user->firstname)
                ->match('/^[a-z ]+$/i')
                ->firstname,
            'last_name' => $req
                ->lastname()
                ->default($this->user->lastname)
                ->match('/^[a-z ]+$/i')
                ->lastname,
            'username' => $req
                ->username()
                ->default($this->user->username)
                ->match('/^[a-z ]+$/i')
                ->tolower()
                ->username,
        ]);

        if($req->image && !is_string($req->image)):
            $image = File::upload([
                'file' => $req->image,
                'path' => 'Public/images/' . $this->user->id
            ]);
            $pipe->avatar = $image['abs_path'];
        endif;
        
        $update = $this->user
            ->updateUser((array) $req)
            ->remove(...userFilters());
        Res::json($update);
    }
}
