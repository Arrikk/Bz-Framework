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
        Res::json($this->user->remove(...userFilters()));
    }

    /**
     * Update User Profile
     * @param Pipes $req... body params
     */
    public function _update(Pipes $req)
    {
        $update = $this->user->updateUser([
            // Add Update Data... Or Pass DTO Pipes
            // 'phone' => Secure($req->phone ?? $this->user->phone),
            // 'first_name' => Secure($req->firstname ?? $this->user->first_name),
            'updated_at' => CURRENT_DATE,
        ])->remove(...userFilters());
        Res::json($update);
    }
}