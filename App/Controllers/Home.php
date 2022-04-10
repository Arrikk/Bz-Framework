<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use App\Controllers\Authenticated\Authenticated;
use Core\Http\Res;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends Authenticated
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function _index($get) # get as GET
    {
        View::draw('{/account/home}', [
            '__user' => $this->user
        ]);
    }


    
    public function test($data) #Data as POST
    {
        Res::json($data);
    }
}
