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
        // Res::send($this->authenticated);
        $piped = $req->pipe([
            'first_name' => $req
                ->firstname()
                ->default($this->user->first_name)
                ->match('/^[a-z ]+$/i')
                ->firstname,
            'last_name' => $req
                ->lastname()
                ->default($this->user->last_name)
                ->match('/^[a-z ]+$/i')
                ->lastname,
            'username' => $req
                ->username()
                ->default($this->user->username)
                ->match('/^[a-z ]+$/i')
                ->tolower()
                ->username,
        ]);

        if(isset($req->image) && !is_string($req->image)):
            $image = File::upload([
                'file' => $req->image,
                'path' => 'Public/images/' . $this->user->id
            ]);
            $piped->avatar = $image['abs_path'];
        endif;
        
        $update = $this->user
            ->updateUser((array) $piped)
            ->remove(...userFilters());
        Res::json($update);
    }
}
