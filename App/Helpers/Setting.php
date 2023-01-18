<?php
namespace App\Helpers;
class Setting
{
    public static function App()
    {
        return (object) [
            'name' => "Coinrimp Dashboard",
            'description' => "Crypto Wallet Management Application, Developed by Bruiz",
            'logo' => '/Public/images/favicon.png',
            'slug' => '',
            'keywords' => 'PHP, Bruiz, 8.0, Nellalink, Zugavalize, Application, Developer, Crypto, Wallet',
            'author' => "Adeyemi Opeyemi",
            'title' => 'Coinrimp Dashboard',
            'url' => '/'
        ];
    }
}