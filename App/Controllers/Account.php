<?php

namespace App\Controllers;

use App\Controllers\Authenticated\Authenticated;
use Core\Http\Res;
use Core\Pipes\Pipes;

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
            'fullname' => $req
                ->fullname()
                ->default($this->user->fullname)
                ->match('/^[a-z ]+$/i')
                ->fullname,
            'username' => $req
                ->username()
                ->default($this->user->username)
                ->match('/^[a-z ]+$/i')
                ->tolower()
                ->username,
        ]);
        
        $update = $this->user
            ->updateUser((array) $req)
            ->remove(...userFilters());
        Res::json($update);
    }
}
