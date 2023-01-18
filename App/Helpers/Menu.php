<?php
namespace App\Helpers;

class Menu
{
    public static function myMenu()
    {
        return [

            translate('Dashboard') => [
                'icon' => 'icon ni ni-dashboard',
                'link' => '/dashboard',
                'other' => ''
            ],
            translate('Buy Game Points') => [
                'icon' => 'icon ni ni-wallet-alt',
                'link' => '/buy-game-points',
                'other' => ''
            ],
            translate('Prices') => [
                'icon' => 'icon ni ni-sign-kobo',
                'link' => '/prices',
                'other' => ''
            ],
            translate('Logout') => [
                'icon' => 'icon ni ni-signout',
                'link' => '/logout',
                'other' => ''],
        ];
    }
}
