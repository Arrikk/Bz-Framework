<?php
namespace App\Models;

use Core\Model\Model;

class Folder extends Model
{
    public static function myFolders(User $user) : array 
    {
        $folders = self::find([
            "user_id" => $user->id,
        ], "*,  DATE_FORMAT(created_at, '%b %d %Y') AS created_on, DATE_FORMAT(updated_at, '%b %d %Y') AS last_updated_on");
        return $folders;
    }
}   