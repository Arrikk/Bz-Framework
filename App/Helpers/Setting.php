<?php
namespace App\Helpers;
class Setting
{
    public static function App()
    {
        return (object) [
            'name' => "",
            'description' => "",
            'logo' => "",
            'slug' => '',
            'keywords' => "",
            'author' => "",
            'title' => '',
            'url' => '/'
        ];
    }
}